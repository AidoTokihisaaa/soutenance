<?php
require_once 'backend\models\Event.php';

class EventController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    public function createEvent($title, $description, $date) {
        return $this->eventModel->addEvent($title, $description, $date) ? "Event created successfully." : "Failed to create event.";
    }

    public function getAllEvents() {
        return $this->eventModel->getEvents();
    }

    public function getEvent($id) {
        return $this->eventModel->getEventById($id);
    }

    public function updateEvent($id, $title, $description, $date) {
        return $this->eventModel->updateEvent($id, $title, $description, $date) ? "Event updated successfully." : "Failed to update event.";
    }

    public function deleteEvent($id) {
        return $this->eventModel->deleteEvent($id) ? "Event deleted successfully." : "Failed to delete event.";
    }
}
?>
