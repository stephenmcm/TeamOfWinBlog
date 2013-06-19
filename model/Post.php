<?php
/**
 * Contains a single Post entry for the blog
 * Features a helper method for building and returning Arrays of posts
 *
 * @author Stephen McMahon <stephentmcm@gmail.com>
 */
class Post extends Article
{
    /**
     * Loads an existing post from the database
     *
     * @param Int $postId the id number of the post
     *
     * @return Boolean   True for loaded false for DB connection error
     * @throws Exception PDO expection
     */
    public function load($postId)
    {

        try {
            $statement = "SELECT * FROM `posts` WHERE `postid` = '$postId'";

            $post = $this->database->query($statement)->fetch();

        } catch (Exception $e) {

            throw new Exception('Database error:', 0, $e);

            return(false);
        };

        $this->post = $post;

        return(true);
    }//end loadPost

    /**
     * Stores the input data in the post object used for creating new post
     *
     * @param Array $post Must contain `title`, `status`, `ACL`, `content`,
     * `username`
     *
     * @return Boolean   True on sucess else false
     * @throws Exception Throws Database and custom errors
     */
    public function create($post = array())
    {
        if (empty($post)) {

            throw new Exception('Create requires an Array of values');

            return(false);
        };

        $keys = array(`title`, `status`, `ACL`, `content`, `username`);

        foreach ($keys as $keys) {
            if (!array_key_exists($key, $post)) {
                throw new Exception('Create requires an value for "'.$key.'"');

                return(false);
            };
        };

        $this->setTitle($post['title']);

        $this->setStatus($post['status']);

        $this->setACL($post['ACL']);

        $this->setContent($post['content']);

        $this->setUsername($post['username']);

        try {

            $statement = "INSERT INTO `posts` ( `title`, `status`, `ACL`, `content`, `username`)
                                       VALUES ( `:title`, `:status`, `:ACL`, `:content`, `:username`)
                          ON DUPLICATE KEY UPDATE
                          title=values(title), status=values(status), ACL=values(ACL), content=values(content),
                          username=values(username) ";

            $query = $this->database->prepare($statement);

            $query->execute($this->postNamedParams());

        } catch (Exception $e) {
            throw new Exception('Database error:', 0, $e);

            return(false);
        };

        return(true);
    }
    
    /**
     * Uses the super cool on duplicate key update MySQL function to update an existing post
     * @return Boolean   True on sucess else false
     * @throws Exception PDO expections on Database errors
     */
    public function save()
    {
        try {

            $statement = "INSERT INTO `posts` (`postid`, `title`, `status`, `ACL`, `content`, `date`, `username`)
                                       VALUES (`:postid`, `:title`, `:status`, `:ACL`, `:content`, `:date`, `:username`)
                          ON DUPLICATE KEY UPDATE
                                    postid=values(postid), title=values(title), status=values(status), ACL=values(ACL),
                                    content=values(content), date=values(date), username=values(username) ";

            $query = $this->database->prepare($statement);

            $query->execute($this->articleNamedParams());

        } catch (Exception $e) {
            throw new Exception('Database error:', 0, $e);

            return(false);
        };

        return(true);
    }//end save

    /**
     * Deletes the current post from the database
     * 
     * @return Boolean Sucess or failure
     * @throws Exception Database exceptions if query fails
     */
    public function delete()
    {
        try {

            $statement = "UPDATE `tow`.`posts` SET `status` = 'deleted'
                          WHERE `postid` = ?;";

            $query = $this->database->prepare($statement);

            $query->execute($this->getPostid());

        } catch (Exception $e) {
            throw new Exception('Database error:', 0, $e);

            return(false);
        };

        return(true);
    }

    //*********SETTERS----------------------
    public function setPostid($param)
    {
        $this->post['postid'] = $param;
    }

    public function setTitle($param)
    {
        $this->post['title'] = $param;
    }

    //*********GETTERS--------------
    public function getPost()
    {
        return($this->post);
    }

    public function getPostid()
    {
        return($this->post['postid']);
    }

    public function getTitle()
    {
        return($this->post['title']);
    }

}//end post class
