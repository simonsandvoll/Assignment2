<?php
class Entry {
    function __construct($id, $title, $content, $createdBy, $topicId) {
        $this->id = $id;      // Primary key
        $this->title = $title;
        $this->content = $content;
        $this->createdBy = $createdBy;
        $this->topicId = $topicId;
    }

    public function __toString(){
        echo "
            <div>
                <h5>$this->title</h5>
                <p>$this->content</p>
                <span>entry created by: $this->createdBy</span>
            </div>";
    }
}
?>