<?php
declare(strict_types=1);
include "Class/Band.php";
include "Class/Song.php";
include "Class/Link.php";
include "Class/Album.php";
include "Class/Member.php";


// --- CONFIG ------------------------------------------------------------------
const DATA_PATH = __DIR__ . '/bands_full.json';
const PAGE_SIZE = 3; // albums per page (cards)
const SHOW_SONGS = 8; // max song rows shown by default, rest on <details>

// Headers
header('Content-Type: text/html; charset=UTF-8');

// Error visibility (toggle off in production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// --- HELPERS -----------------------------------------------------------------
function read_json(string $path): array
{
    if (!is_file($path)) {
        http_response_code(500);
        exit("Missing data file: " . basename($path));
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        http_response_code(500);
        exit("Failed to read data file.");
    }
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data)) {
        http_response_code(500);
        exit("Invalid JSON structure.");
    }
    return $data;
}

/**
 * Safe HTML escape.
 */
function esc(?string $s): string
{
    if ($s === null) return '';
    // $s = mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8'); // if using ISO pages
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function array_get(array $a, string $k, $default = null)
{
    return $a[$k] ?? $default;
}

function norm_string(string $s): string
{
    return mb_strtolower(trim($s), 'UTF-8');
}

function contains(string $hay, string $needle): bool
{
    return mb_strpos(norm_string($hay), norm_string($needle)) !== false;
}

function unique_genres(array $bands): array
{
    $g = [];
    foreach ($bands as $b) {
        foreach ((array)array_get($b, 'genres', []) as $genre) {
            $g[$genre] = true;
        }
    }
    ksort($g, SORT_NATURAL);
    return array_keys($g);
}

function unique_members(array $bands): array
{
    $m = [];
    foreach ($bands as $b) {
        foreach ((array)array_get($b, 'members', []) as $member) {
            if(array_key_exists($member['name'], $m)){
                continue;
            }
            $m[$member['name']] = true;
        }
    }
    ksort($m, SORT_NATURAL);
    return array_keys($m);
}

/**
 * Flatten albums with their band reference for pagination.
 */
function flatten_albums(array $bands): array
{
    $out = [];
    foreach ($bands as $bandIdx => $band) {
        $bandName = array_get($band, 'name', 'Unknown');
        $links = array_get($band, 'links', []);
        foreach ((array)array_get($band, 'albums', []) as $albumIdx => $album) {
            $album['_band'] = [
                'name' => $bandName,
                'genres' => array_get($band, 'genres', []),
                'links' => $links,
                'members' => array_get($band, 'members', []),
                'founded' => array_get($band, 'founded', null),
                'origin' => array_get($band, 'origin', null),
            ];
            $album['_id'] = sprintf('%d-%d', $bandIdx, $albumIdx);
            $out[] = $album;
        }
    }
    return $out;
}

/**
 * Filter albums by query and genre.
 */
function filter_albums(array $albums, ?string $q, ?string $genre, ?string $member): array
{
    $res = [];
    foreach ($albums as $al) {
        $match = true;

        if ($genre && $genre !== 'all') {
            $bandGenres = (array)array_get($al['_band'], 'genres', []);
            $match = in_array($genre, $bandGenres, true);
            if (!$match) continue;
        }

        if($member && $member !== 'all'){
            $members = (array)array_get($al['_band'], 'members', []);
            $temp_member = [];
            foreach($members as $mem){
                $temp_member[] = $mem["name"];
            }
            $match = in_array($member, $temp_member, true);
            if (!$match) continue;
        }

        if ($q) {
            $qMatch = false;
            $bandName = (string)array_get($al['_band'], 'name', '');
            $albumTitle = (string)array_get($al, 'title', '');
            $songs = (array)array_get($al, 'songs', []);

            if (contains($bandName, $q) || contains($albumTitle, $q)) {
                $qMatch = true;
            } else {
                foreach ($songs as $s) {
                    if (contains((string)array_get($s, 'title', ''), $q)) {
                        $qMatch = true;
                        break;
                    }
                }
            }
            $match = $match && $qMatch;
        }

        if ($match) $res[] = $al;
    }
    return $res;
}

/**
 * Pagination.
 */
function paginate(array $items, int $page, int $perPage): array
{
    $total = count($items);
    $pages = max(1, (int)ceil($total / max(1, $perPage)));
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $perPage;
    $slice = array_slice($items, $offset, $perPage);
    return [$slice, $page, $pages, $total];
}

// --- LOAD --------------------------------------------------------------------
try {
    $data = read_json(DATA_PATH);
} catch (Throwable $e) {
    http_response_code(500);
    exit("JSON parse error: " . esc($e->getMessage()));
}

$bands = (array)array_get($data, 'bands', []);
$genresAll = unique_genres($bands);
$membersAll = unique_members($bands);
$albumsFlat = flatten_albums($bands);

// --- INPUTS ------------------------------------------------------------------
$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$genre = isset($_GET['genre']) ? (string)$_GET['genre'] : 'all';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$member = isset($_GET['member']) ? (string)$_GET['member'] : '';

$albumsFiltered = filter_albums($albumsFlat, $q ?: null, $genre ?: null, $member ?: null);
[$albumsPage, $page, $pages, $total] = paginate($albumsFiltered, $page, PAGE_SIZE);

// --- VIEW --------------------------------------------------------------------
?>
<!doctype html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Artists & Albums</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --bg: #0f1220;
            --fg: #e9eef9;
            --muted: #a9b3c7;
            --card: #171a2b;
            --accent: #7aa2ff;
            --chip: #2a2f47;
            --ok: #2ecc71;
            --warn: #f1c40f;
            --danger: #e74c3c;
            --border: #252a40;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font: 16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Helvetica Neue", Arial;
            background: linear-gradient(180deg, #0d1020 0%, #0f1220 100%);
            color: var(--fg)
        }

        header {
            position: sticky;
            top: 0;
            background: rgba(15, 18, 32, .8);
            backdrop-filter: saturate(120%) blur(8px);
            border-bottom: 1px solid var(--border);
            z-index: 10
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px
        }

        h1 {
            margin: 0 0 6px;
            font-size: 22px
        }

        form.filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px
        }

        input[type="text"], select {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--fg);
            padding: 10px 12px;
            border-radius: 10px;
            outline: none;
            min-width: 240px;
        }

        button {
            background: var(--accent);
            color: #0a0d1a;
            border: 0;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 20px
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .25);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: transform .08s ease, box-shadow .12s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .35)
        }

        .badge {
            display: inline-block;
            background: var(--chip);
            color: var(--fg);
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            margin-right: 6px
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap
        }

        .muted {
            color: var(--muted)
        }

        .title {
            font-weight: 800;
            font-size: 18px
        }

        .band {
            font-weight: 600
        }

        .songs {
            border-top: 1px dashed var(--border);
            padding-top: 8px;
            margin-top: 4px
        }

        .songs ol {
            margin: 6px 0 0 18px;
            padding: 0
        }

        .songs li {
            margin: 2px 0
        }

        .links a {
            color: var(--accent);
            text-decoration: none
        }

        .links a:hover {
            text-decoration: underline
        }

        .meta {
            font-size: 13px;
            color: var(--muted)
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px
        }

        nav.pager {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            margin: 22px 0
        }

        nav.pager a, nav.pager span {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 12px;
            text-decoration: none;
            color: var(--fg)
        }

        nav.pager .active {
            background: var(--accent);
            color: #0a0d1a;
            border-color: transparent
        }

        .count {
            font-size: 14px;
            color: var(--muted);
            margin-top: 12px
        }

        details summary {
            cursor: pointer;
            color: var(--accent)
        }
    </style>
</head>
<body>
<header>
    <div class="wrap">
        <h1>Bändit & Albumit</h1>
        <form class="filters" method="get">
            <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Haku: bändi, albumi tai kappale…">
            <select name="genre" aria-label="Genre">
                <option value="all" <?= $genre === 'all' ? 'selected' : ''; ?>>Kaikki genret</option>
                <?php foreach ($genresAll as $g): ?>
                    <option value="<?= esc($g) ?>" <?= $genre === $g ? 'selected' : ''; ?>><?= esc($g) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="member" aria-label="Jäsen">
                <option value="all" <?= $member === 'all' ? 'selected' : ''; ?>>Kaikki jäsenet</option>
                <?php foreach ($membersAll as $g): ?>
                    <option value="<?= esc($g) ?>" <?= $member === $g ? 'selected' : ''; ?>><?= esc($g) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Suodata</button>
            <?php if ($q || ($genre && $genre !== 'all') || ($member && $member !== 'all')): ?>
                <a href="?" class="badge">Tyhjennä</a>
            <?php endif; ?>
        </form>
        <div class="count">
            Tuloksia: <?= (int)$total; ?> albumia – sivu <?= (int)$page; ?>/<?= (int)$pages; ?>
        </div>
    </div>
</header>

<main class="wrap">
    <section class="grid">
        <?php foreach ($albumsPage as $al): ?>
            <?php
            $band = $al['_band'];
            $songs = (array)array_get($al, 'songs', []);
            $primaryLinks = array_filter([
                'Kotisivu' => array_get($band['links'], 'website'),
                'Wikipedia' => array_get($band['links'], 'wikipedia'),
                'Spotify' => array_get($band['links'], 'spotify'),
                'YouTube' => array_get($band['links'], 'youtube'),
            ]);
            $songCount = count($songs);
            ?>
            <article class="card" id="a-<?= esc($al['_id']) ?>">
                <div class="title"><?= esc((string)array_get($al, 'title', '(album)')) ?></div>
                <div class="band"><?= esc((string)array_get($band, 'name', '(bändi)')) ?></div>
                <div class="row meta">
                    <span><?= esc((string)array_get($al, 'genre', '')) ?></span>
                    <span>&middot;</span>
                    <span><?= (int)array_get($al, 'release_year', 0) ?: '–' ?></span>
                    <?php if (!empty($band['origin'])): ?>
                        <span>&middot;</span><span><?= esc((string)$band['origin']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($band['genres'])): ?>
                    <div class="chips">
                        <?php foreach ($band['genres'] as $bg): ?>
                            <span class="badge"><?= esc((string)$bg) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($band['members'])): ?>
                    <div class="meta">
                        <strong>Jäsenet:</strong>
                        <?php
                        $names = array_map(fn($m) => array_get($m, 'name', '?') . (array_get($m, 'role') ? " (" . array_get($m, 'role') . ")" : ''), $band['members']);
                        echo esc(implode(', ', $names));
                        ?>
                    </div>
                <?php endif; ?>

                <div class="songs">
                    <?php if ($songCount <= SHOW_SONGS): ?>
                        <ol>
                            <?php foreach ($songs as $s): ?>
                                <li><?= esc((string)array_get($s, 'title', '(kappale)')) ?>
                                    <span class="muted">— <?= esc((string)array_get($s, 'length', '–:–')) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <ol>
                            <?php foreach (array_slice($songs, 0, SHOW_SONGS) as $s): ?>
                                <li><?= esc((string)array_get($s, 'title', '(kappale)')) ?>
                                    <span class="muted">— <?= esc((string)array_get($s, 'length', '–:–')) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <details>
                            <summary>Näytä loput (<?= (int)($songCount - SHOW_SONGS) ?>)</summary>
                            <ol start="<?= SHOW_SONGS + 1 ?>">
                                <?php foreach (array_slice($songs, SHOW_SONGS) as $s): ?>
                                    <li><?= esc((string)array_get($s, 'title', '(kappale)')) ?>
                                        <span class="muted">— <?= esc((string)array_get($s, 'length', '–:–')) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </details>
                    <?php endif; ?>
                </div>

                <div class="footer">
                    <div class="links">
                        <?php foreach ($primaryLinks as $label => $url): ?>
                            <a href="<?= esc($url) ?>" target="_blank" rel="noopener"><?= esc($label) ?></a>
                            <?php if ($label !== array_key_last($primaryLinks)) echo ' · '; ?>
                        <?php endforeach; ?>
                    </div>
                    <a class="badge" href="#top">↑ Ylös</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <?php if ($pages > 1): ?>
        <nav class="pager" aria-label="Sivutus">
            <?php
            // path without queryparams
            $basePath   = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
            if ($basePath === '' || $basePath === false) {
                $basePath = $_SERVER['PHP_SELF'] ?? '/';
            }

            // Current GET paramems -> dont carry page
            $baseParams = $_GET;
            unset($baseParams['page']);

            $urlForPage = function (int $p) use ($baseParams, $basePath): string {
                $params = $baseParams;
                if ($p > 1) {
                    $params['page'] = $p;
                }
                $query = http_build_query($params, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
                // Return always path + query, never emtpy href.
                return $basePath . ($query ? ('?' . $query) : '');
            };

            $link = function (int $p, ?string $label = null, bool $active = false) use ($urlForPage): string {
                $href = $urlForPage($p);
                $cls  = $active ? 'active' : '';
                $txt  = $label ?? (string)$p;
                return '<a class="'.$cls.'" href="'.htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'">'.$txt.'</a>';
            };

            // Previous
            echo ($page > 1) ? $link($page - 1, 'Edellinen') : ($page == 1 ? '' : '<span>Edellinen</span>');

            // Compact window
            $window = 2;
            $start  = max(1, $page - $window);
            $end    = min($pages, $page + $window);

            if ($start > 1) {
                echo $link(1);
                if ($start > 2) echo '<span>…</span>';
            }

            for ($i = $start; $i <= $end; $i++) {
                echo $link($i, null, $i === $page);
            }

            if ($end < $pages) {
                if ($end < $pages - 1) echo '<span>…</span>';
                echo $link($pages);
            }

            // Next
            echo ($page < $pages) ? $link($page + 1, 'Seuraava') : ($page == $pages ? '' : '<span>Seuraava</span>');
            ?>
        </nav>
    <?php endif; ?>


</main>

<footer class="wrap" style="opacity:.8;margin-bottom:40px">
    <div class="muted">Data: bands_full.json · <?= esc(date('Y-m-d H:i')) ?></div>
</footer>
</body>
</html>
