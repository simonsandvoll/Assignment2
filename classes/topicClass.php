<?php
class Topic {
    function __construct($id, $title, $description, $createdBy, $entryCount) {
        $this->id = $id;      // Primary key
        $this->title = $title;
        $this->description = $description;
        $this->createdBy = $createdBy;
        $this->entryCount = $entryCount;
    }

    public function __toString(){
            echo "
            <h3>$this->title</h3>
            <p>$this->description</p>
            <span>created By: $this->createdBy</span><br>
            <h4>Entries: </h4>
            ";
    }
}
?>