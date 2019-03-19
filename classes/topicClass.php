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
            <a href='./src/showEntries.php?id=$this->id'>Show All Entries($this->entryCount)</a>
            <h4>Entries: </h4>
            ";
    }
}
?>