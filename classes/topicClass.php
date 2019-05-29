<?php
class Topic {
    function __construct($id, $title, $description, $createdBy, $entryCount) {
        $this->id = $id;      // Primary key
        $this->title = $title;
        $this->description = $description;
        $this->createdBy = $createdBy;
        $this->entryCount = $entryCount;
    }

    /**
     * ECHO info about that specific topic
    */
    public function __toString(){
            echo "
            <h3>$this->title</h3>
            <p>$this->description</p>
            <p>created By: $this->createdBy</p><br>
            <h4>Entries: </h4>
            ";
    }
}
?>