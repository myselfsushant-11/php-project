<?php
require_once __DIR__ . '/../../includes/auth.php';

if (!is_admin_logged_in()) {
    json_out(['success' => false, 'message' => 'Admin login required.'], 401);
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'list': {
        $q = trim($_GET['q'] ?? '');

        if ($q !== '') {
            $stmt = $pdo->prepare(
                "SELECT * FROM movies WHERE title LIKE ? ORDER BY created_at DESC"
            );
            $stmt->execute(['%' . $q . '%']);
        } else {
            $stmt = $pdo->query(
                "SELECT * FROM movies ORDER BY created_at DESC"
            );
        }

        json_out([
            'success' => true,
            'movies' => $stmt->fetchAll()
        ]);

        break;
    }


    case 'save': {
        $id          = (int) ($_POST['id'] ?? 0);
        $title       = clean($_POST['title'] ?? '');
        $genre       = clean($_POST['genre'] ?? '');
        $language    = clean($_POST['language'] ?? '');
        $duration    = (int) ($_POST['duration'] ?? 0);
        $rating      = (float) ($_POST['rating'] ?? 0);
        $description = clean($_POST['description'] ?? '');
        $trailer     = clean($_POST['trailer_url'] ?? '');
        $releaseDate = $_POST['release_date'] ?? null;

        $status = in_array(
            $_POST['status'] ?? '',
            ['now_showing', 'coming_soon']
        )
            ? $_POST['status']
            : 'now_showing';


        if (
            $title === '' ||
            $genre === '' ||
            $language === '' ||
            $duration <= 0
        ) {
            json_out([
                'success' => false,
                'message' => 'Please fill in all required fields.'
            ]);
        }


        $posterPath   = null;
        $backdropPath = null;

        $destDir = __DIR__ . '/../../assets/images/movies';


        if (!empty($_FILES['poster']['name'])) {
            $posterPath = upload_image(
                $_FILES['poster'],
                $destDir,
                'poster'
            );

            if (!$posterPath) {
                json_out([
                    'success' => false,
                    'message' => 'Invalid poster image.'
                ]);
            }

            $posterPath = 'assets/images/movies/' . basename($posterPath);
        }


        if (!empty($_FILES['backdrop']['name'])) {
            $backdropPath = upload_image(
                $_FILES['backdrop'],
                $destDir,
                'backdrop'
            );

            if (!$backdropPath) {
                json_out([
                    'success' => false,
                    'message' => 'Invalid backdrop image.'
                ]);
            }

            $backdropPath = 'assets/images/movies/' . basename($backdropPath);
        }


        if ($id > 0) {

            // Edit
            $fields = "title=?, genre=?, language=?, duration=?, rating=?, description=?, trailer_url=?, release_date=?, status=?";

            $params = [
                $title,
                $genre,
                $language,
                $duration,
                $rating,
                $description,
                $trailer,
                $releaseDate,
                $status
            ];

            if ($posterPath) {
                $fields .= ", poster=?";
                $params[] = $posterPath;
            }

            if ($backdropPath) {
                $fields .= ", backdrop=?";
                $params[] = $backdropPath;
            }

            $params[] = $id;

            $stmt = $pdo->prepare(
                "UPDATE movies SET $fields WHERE id = ?"
            );

            $stmt->execute($params);

            json_out([
                'success' => true,
                'message' => 'Movie updated successfully.'
            ]);

            break;

        } else {

            // Add
            $stmt = $pdo->prepare(
                "INSERT INTO movies
                (
                    title,
                    genre,
                    language,
                    duration,
                    rating,
                    poster,
                    backdrop,
                    description,
                    trailer_url,
                    release_date,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $title,
                $genre,
                $language,
                $duration,
                $rating,
                $posterPath,
                $backdropPath,
                $description,
                $trailer,
                $releaseDate,
                $status
            ]);

            json_out([
                'success' => true,
                'message' => 'Movie added successfully.'
            ]);

            break;
        }
    }


    case 'delete': {
        $id = (int) ($_POST['id'] ?? 0);

        if (!$id) {
            json_out([
                'success' => false,
                'message' => 'Invalid movie.'
            ]);
        }

        $stmt = $pdo->prepare(
            'DELETE FROM movies WHERE id = ?'
        );

        $stmt->execute([$id]);

        json_out([
            'success' => true,
            'message' => 'Movie deleted.'
        ]);

        break;
    }


    default:
        json_out([
            'success' => false,
            'message' => 'Unknown action.'
        ], 400);

        break;
}