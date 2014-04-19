<?php
/**
 * This is the controller.
 * PHP 5.4+ required
 *
 * @author Michael C. Butler
 * @created 2014-04-04
 *
 * Running this file in browser should launch the project.
 */

require './settings.php';
require './utilities.php';
require './models/model.php';
require './models/repository.php';

ini_set('session.gc_maxlifetime', 3600 * 24 * 365);
ini_set('session.cookie_lifetime', 3600 * 24 * 365);
session_start();

class BookmarkController {

    protected $view = './views/view.php';
    protected $urlParam = 'bookmarks';

    /** @var Repository */
    protected $repository;

    public function __construct (Repository $repository) {
        $this->repository = $repository;
    }

    public function start () {
        $isAjaxRequest = strpos($_SERVER['HTTP_ACCEPT'], '/json') !== false;

        if (!$isAjaxRequest) {
            $this->showInterface();
            return;
        }

        try {
            $this->handleAjaxRequest();
        }
        catch (Exception $e) {
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * HTML outputting function
     * Show initial page load for user interface with bookmarks from Database
     */
    public function showInterface () {
        $bookmarks = $this->repository->getBookmarks($this->getUserId());
        $bookmarkConfig = json_encode($bookmarks);
        include $this->view;
    }


    /**
     * Basic REST API with JSON output
     * @throws Exception
     */
    protected function handleAjaxRequest () {
        $urlParts = $this->getUrlParts();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if (empty($urlParts) || $urlParts[0] !== $this->urlParam) {
            throw new Exception('Invalid URL. Expecting /bookmarks/');
        }

        if ($requestMethod === 'GET') {
            if (empty($urlParts[1])) {
                $this->listBookmarks();
            }
            else {
                // This method is untested because I could not find a front-end purpose for it
                $this->listBookmark($urlParts[1]);
            }
        }
        else if ($requestMethod === 'DELETE') {
            if (empty($urlParts[1])) {
                throw new Exception('Invalid Bookmark ID in URL. Expecting /bookmarks/999999');
            }
            $this->deleteBookmark($urlParts[1]);
        }
        else if ($requestMethod === 'POST') {
            $this->addBookmark();
        }
        else {
            throw new Exception('HTTP Request Method Not Supported');
        }
    }

    /**
     * @return array
     */
    public function getUrlParts () {
        $url = trim(array_get($_SERVER, 'PATH_INFO', ''), '/');
        $urlParts = explode('/', $url);
        return $urlParts;
    }

    /**
     * JSON function
     */
    public function listBookmarks () {
        $bookmarks = $this->repository->getBookmarks($this->getUserId());
        echo json_encode($bookmarks);
    }

    /**
     * JSON function
     * NOTE: This function is not currently used, as I could not find a purpose for it.
     */
    public function listBookmark ($displayId) {
        $bookmark = $this->repository->getBookmark($this->getUserId(), $displayId);
        echo json_encode($bookmark);
    }

    /**
     * JSON function
     * Handles Creating a new bookmark and also editing existing
     */
    public function addBookmark () {
        $bookmark = new BookmarkModel();
        $payload = json_decode(file_get_contents('php://input'));
        $bookmark->setUserId($this->getUserId());
        $bookmark->url = $payload->url;
        $bookmark->title = $payload->title;
        $bookmark->display_id = $payload->display_id;

        if (empty($bookmark->display_id)) {
            throw new Exception('Invalid bookmark id.');
        }

        $id = $bookmark->store();

        if (!$id) {
            throw new Exception('Failed storing bookmark in database.');
        }

        echo json_encode([
            'success' => true,
            'display_id' => $bookmark->display_id
        ]);
    }

    /**
     * JSON function
     */
    public function deleteBookmark ($displayId = null) {
        $bookmark = new BookmarkModel();
        $bookmark->display_id = $displayId;
        $bookmark->setUserId($this->getUserId());
        $affectedRows = $bookmark->delete();
        echo json_encode([
            'success' => ($affectedRows > 0),
            'display_id' => $bookmark->display_id
        ]);
    }

    public function getUserId () {
        return session_id();
    }
}

$repo = Repository::getRepository();
$bookmarkManager = new BookmarkController($repo);
$bookmarkManager->start();
