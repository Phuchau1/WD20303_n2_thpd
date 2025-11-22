<?php
class Category {
    private $db;
    public function __construct($db){
        // $db is expected to be Database instance
        $this->db = $db;
    }

    public function getAll(){
        return $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    public function find($id){
        return $this->db->queryOne("SELECT * FROM categories WHERE id = ?", [(int)$id]);
    }

    public function create($name){
        return $this->db->execute("INSERT INTO categories (name) VALUES (?)", [$name]);
    }

    public function delete($id){
        return $this->db->execute("DELETE FROM categories WHERE id = ?", [(int)$id]);
    }

        // Get category by name (case-insensitive)
        public function getByName($name){
            // Use case-insensitive comparison to tolerate capitalization differences
            return $this->db->queryOne("SELECT * FROM categories WHERE LOWER(name) = LOWER(?)", [$name]);
        }

        /**
         * Get category by slug
         */
        public function getBySlug($slug){
            return $this->db->queryOne("SELECT * FROM categories WHERE slug = ?", [$slug]);
        }
}
?>
