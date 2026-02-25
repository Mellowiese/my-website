<?php

class Book {
    public $title;
    public $author;
    public $year;
    public $Available;
    
    public function __construct($title, $author, $year, $available = true) {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->Available = $available;
    }

    public static function create($title, $author, $year, $available = true) {
        return new self($title, $author, $year, $available);
    } 

    public function getTitle() {
        return $this->title;
    }   

    public function getAuthor() {
        return $this->author;
    }

    public function getYear() {
        return $this->year;
    }

    public function isAvailable() {
        return $this->Available;
    }

    public function __toString() {
        return "Title: " . $this->title . ", Author: " . $this->author . ", Year: " . $this->year . ", Available: " . ($this->Available ? "Yes" : "No");
    }

    public function displayInfo() {
        echo $this->title . " by " . $this->author . " (" . $this->year . ") - " . ($this->Available ? "Available" : "Not Available") . "\n";
    }   
}
echo "<pre>";

$book1=new Book("The Great Gatsby", "F. Scott Fitzgerald", 1925);
$book2=new Book("To Kill a Mockingbird", "Harper Lee", 1960, false);

$book1->displayInfo();
$book2->displayInfo();

echo "</pre>";
?>