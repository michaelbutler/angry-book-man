<?php

/**
 * This is the model.
 * @author Michael C. Butler
 * @created 2014-04-04
 */
class BookmarkModel {

    protected $id;
    protected $user_id;

    public $url;
    public $date_created;
    public $title;
    public $display_id;

    /**
     * This combines updating and inserting
     *
     * @return int Returns the primary key (id) of the Bookmark on INSERT, or number of rows affected on UPDATE
     */
    function store () {
        $repo = Repository::getRepository();
        if (!$repo->exists($this)) {
            return $repo->insert($this);
        }
        else {
            return $repo->update($this);
        }
    }

    /**
     * @return int|null Number of rows deleted on success, null on failure
     */
    function delete () {
        if (empty($this->display_id) || empty($this->user_id)) {
            return null;
        }
        $repo = Repository::getRepository();
        return $repo->delete($this);
    }

    /**
     * @return int
     */
    function addBookmark () {
        return $this->store();
    }

    /**
     * @return int|null
     */
    function deleteBookmark () {
        return $this->delete();
    }

    function editBookmark () {
        return $this->store();
    }

    /**
     * @return string $user_id
     */
    public function getUserId () {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     */
    public function setUserId ($user_id) {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getId () {
        return $this->id;
    }
}
