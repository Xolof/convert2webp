<?php

/**
 * Db
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

use Convert2Webp\C2wException\TableNotFoundException;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Db
 *
 * Handles database queries.
 */
class Db
{
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Search and replace an image filename.
     *
     * @param string $before
     * @param string $after
     * @return boolean
     */
    public function searchReplace(string $before, string $after): void
    {
        $exploded = explode(".", $before);
        $mimetypeBefore = end($exploded);

        if ($mimetypeBefore === "jpg") {
            $mimetypeBefore = "jpeg";
        }

        $this->searchReplaceTablePosts($before, $after, $mimetypeBefore);
        $this->searchReplaceTablePostmeta($before, $after, $mimetypeBefore);
    }

    /**
     * Search and replace in table 'posts'.
     */
    protected function searchReplaceTablePosts(string $before, string $after, string $mimetypeBefore): void
    {
        $table = $this->getTableNameWithPrefix('posts');

        $query = <<<SQL
        UPDATE %i
        SET post_content = REPLACE(post_content, '%s', '%s')
        WHERE post_content LIKE CONCAT('%', "%s", '%');
        SQL;

        $this->execute($query, $table, $before, $after);

        $query = <<<SQL
        UPDATE %i
        SET guid = REPLACE(guid, '%s', '%s')
        WHERE guid LIKE CONCAT('%', "%s", '%')
        AND post_type = 'attachment';
        SQL;

        $this->execute($query, $table, $before, $after);

        $mimetypeQuery = <<<SQL
        UPDATE %i
        SET post_mime_type = REPLACE(post_mime_type, CONCAT("image/", "%s"), 'image/webp')
        WHERE guid LIKE CONCAT('%', '%s', '%');
        SQL;

        $this->executeMetaData($mimetypeQuery, $table, $mimetypeBefore, $after);
    }

    /**
     * Search and replace in table 'postmeta'.
     */
    protected function searchReplaceTablePostmeta(string $before, string $after, string $mimetypeBefore): void
    {
        $table = $this->getTableNameWithPrefix('postmeta');

        $query = <<<SQL
        UPDATE %i
        SET meta_value = REPLACE(meta_value, "%s", "%s")
        WHERE meta_key = '_wp_attached_file'
        AND meta_value LIKE CONCAT('%', '%s', '%');
        SQL;

        $this->execute($query, $table, $before, $after);

        $query = <<<SQL
        UPDATE %i
        SET meta_value = REPLACE(meta_value, '%s', '%s')
        WHERE meta_key = '_wp_attachment_metadata'
        AND meta_value LIKE CONCAT('%', '%s', '%');
        SQL;

        $this->execute($query, $table, $before, $after);

        $mimetypeQuery = <<<SQL
        UPDATE %i
        SET meta_value = REPLACE(meta_value, CONCAT("image/", "%s"), 'image/webp')
        WHERE meta_value LIKE CONCAT('%', '%s', '%');
        SQL;

        $this->executeMetaData($mimetypeQuery, $table, $mimetypeBefore, $after);
    }

    /**
     * Execute the query.
     */
    protected function execute(string $query, string $table, string $before, string $after): void
    {
        $this->db->get_results(
            $this->db->prepare(
                $query,
                [$table, $before, $after, $before]
            )
        );
    }

    /**
     * Special query for updating the image meta data.
     */
    protected function executeMetaData(
        string $mimetypeQuery,
        string $table,
        string $mimetypeBefore,
        string $after
    ): void {
        $this->db->get_results(
            $this->db->prepare(
                $mimetypeQuery,
                [$table, $mimetypeBefore, $after]
            )
        );
    }

    /**
     * Get the tablename prepended with the database prefix. This is
     * needed because WordPress prefixes tables so that it is possible
     * to have multiple instances of WordPress in the same database.
     *
     * @param string $tablename_without_prefix The tablename without prefix.
     * @return string
     *
     * @throws TableNotFoundException Throws TableNotFoundException if the table does not exist.
     */
    public function getTableNameWithPrefix(string $tablename_without_prefix): string
    {
        global $wpdb;
        $prefix             = $wpdb->prefix;
        $prefixed_tablename = $prefix . $tablename_without_prefix;

        $table_exists = $wpdb->get_results(
            $wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $prefixed_tablename
            )
        );

        if (count($table_exists)) {
            return $prefixed_tablename;
        }

        throw new TableNotFoundException(esc_html("Table $prefixed_tablename not found."));
    }
}
