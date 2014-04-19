<?php

class Repository {

    /** @var PDO */
    protected static $database;

    /** @var Repository */
    protected static $instance;

    /**
     * @return Repository
     */
    public static function getRepository () {
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }

    /**
     * @return PDO
     */
    protected function getDatabase () {
        if (!empty(self::$database)) {
            return self::$database;
        }
        try {
            self::$database = new PDO(DATABASE_CONNECTION, DATABASE_USERNAME, DATABASE_PASSWORD);
            return self::$database;
        }
        catch (PDOException $e) {
            print "Database Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * @param string $userId
     * @throws Exception
     * @return BookmarkModel[]|null
     */
    function getBookmarks ($userId) {
        if (empty($userId)) {
            throw new Exception('Invalid User Id. Cannot find bookmarks.');
        }
        $pdo = $this->getDatabase();
        $statement = $pdo->prepare(
            'SELECT url, title, display_id
            FROM bookmark
            WHERE user_id = :user_id
            ORDER BY bookmark.rank ASC');
        $statement->execute([':user_id' => $userId]);
        $bookmarks = $statement->fetchAll(PDO::FETCH_CLASS, 'BookmarkModel');
        if (!empty($bookmarks)) {
            return $bookmarks;
        }
        return null;
    }

    /**
     * @param $userId
     * @param $displayId
     *
     * @return BookmarkModel|null
     */
    function getBookmark ($userId, $displayId) {
        $pdo = $this->getDatabase();
        $statement = $pdo->prepare(
            'SELECT url, title, display_id
            FROM bookmark
            WHERE user_id = :user_id
            AND display_id = :display_id');
        $statement->execute([
            ':user_id' => $userId,
            ':display_id' => $displayId,
        ]);
        $bookmarks = $statement->fetchAll(PDO::FETCH_CLASS, 'BookmarkModel');
        if (!empty($bookmarks)) {
            return array_shift($bookmarks);
        }
        return null;
    }

    public function insert (BookmarkModel $bookmark) {
        $pdo = $this->getDatabase();
        $statement = $pdo->prepare(
            'INSERT INTO bookmark (url, date_created, title, user_id, display_id)
            VALUES (:url, NOW(), :title, :user_id, :display_id)'
        );
        $statement->execute([
            ':url' => trim($bookmark->url),
            ':title' => trim($bookmark->title),
            ':user_id' => $bookmark->getUserId(),
            ':display_id' => $bookmark->display_id,
        ]);
        return $pdo->lastInsertId();
    }

    public function update (BookmarkModel $bookmark) {
        $pdo = $this->getDatabase();
        $statement = $pdo->prepare('UPDATE bookmark
                SET url = :url,
                title = :title
                WHERE
                display_id = :display_id
                AND user_id = :user_id
                ');
        $statement->execute([
            ':url' => trim($bookmark->url),
            ':title' => trim($bookmark->title),
            ':user_id' => $bookmark->getUserId(),
            ':display_id' => $bookmark->display_id,
        ]);
        return $statement->rowCount();
    }

    public function delete (BookmarkModel $bookmark) {
        $pdo = $this->getDatabase();
        $statement = $pdo->prepare(
            'DELETE FROM bookmark
            WHERE display_id = :display_id
            AND user_id = :user_id');
        $statement->execute([
            ':user_id' => $bookmark->getUserId(),
            ':display_id' => $bookmark->display_id,
        ]);
        return $statement->rowCount();
    }

    /**
     * @param BookmarkModel $bookmark
     *
     * @return bool True if the bookmark exists in the database
     */
    public function exists (BookmarkModel $bookmark) {
        $model = $this->getBookmark($bookmark->getUserId(), $bookmark->display_id);
        return !empty($model);
    }
}