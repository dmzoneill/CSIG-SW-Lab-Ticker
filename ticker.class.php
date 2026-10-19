<?php

namespace LabTicker;

use kyDepartment;
use kyTicket;
use kyTicketStatus;
use kyTicketType;

class Ticker
{
    /**
     * @param mixed $ajax
     */
    public function __construct($ajax)
    {
        if ($ajax == 1) {
            $view = new View("indexsimple");
        } else {
            $view = new View("index");
        }

        $items = array(
            "callback_ticketlist" => array( "ticketentry" , $this , "callbackTicketlist" ),
            "title" => "Lab ticker",
            "refreshTime" => configValue('__REFRESH_TIME')
        );

        $view->process($items);
        $view->output();
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private function getDeptExclusions(): array
    {
        $depart_excl = array();
        $exclusions = (string) configValue('__DEPARTMENT_EXCLUSIONS');

        if (strpos($exclusions, "|") > 0) {
            $depts = explode("|", $exclusions);

            foreach ($depts as $dept) {
                array_push($depart_excl, array("!=", $dept));
            }
        } else {
            array_push($depart_excl, array("!=", $exclusions));
        }

        return $depart_excl;
    }

    /**
     * @param object $a
     * @param object $b
     */
    private static function sortBy($a, $b): int
    {
        $a_status = $a->getStatus()->getTitle();
        $b_status = $b->getStatus()->getTitle();

        $one = null;
        $two = null;

        $sortMethod = configValue('__SORT_METHOD');
        if ($sortMethod == "ID") {
            $one = $a->getId();
            $two = $b->getId();
        } elseif ($sortMethod == "CREATION_DATE") {
            $one = $a->getCreationTime();
            $two = $b->getCreationTime();
        }

        if ($one == $two) {
            return 0;
        }

        if (configValue('__SORT_DIRECTION') == "DESC") {
            return ($one < $two ) ? 1 : -1;
        }

        return ($one < $two ) ? -1 : 1;
    }

    private function filterByDepts(): \kyResultSet
    {
        $departments = kyDepartment::getAll();

        foreach ($this->getDeptExclusions() as $value) {
            $departments = $departments->filterByTitle($value);
        }

        return $departments;
    }

    /**
     * @param array<int, object> $tickets
     * @param mixed $departments
     * @param mixed $status
     * @param mixed $type
     */
    private function appendMatchingTickets(array &$tickets, $departments, $status, $type): void
    {
        $statusList = kyTicketStatus::getAll();
        /** @var \kyResultSet $filteredStatus */
        $filteredStatus = $statusList->{'filterByTitle'}($status);
        $typeList = kyTicketType::getAll();
        /** @var \kyResultSet $filteredType */
        $filteredType = $typeList->{'filterByTitle'}($type);
        $ticketsearch = kyTicket::getAll($departments, $filteredStatus);
        /** @var \kyResultSet $ticketsearch */
        $ticketsearch = $ticketsearch->{'filterByTypeId'}($filteredType->first()->getId());

        if (count($ticketsearch->getRawArray()) > 0) {
            $ticketsfound = $ticketsearch->getRawArray();
            usort($ticketsfound, function ($a, $b) {
                return self::sortBy($a, $b);
            });
            $tickets = array_merge($tickets, $ticketsfound);
        }
    }

    /**
     * @param mixed $value
     * @return list<string>
     */
    private function listFilterValues($value): array
    {
        $value = (string) $value;
        if (strpos($value, "|") > 0) {
            return explode("|", $value);
        }

        return array( $value );
    }

    public function callbackTicketlist(string $match): string
    {
        $itemlist = "";

        $tickets = array();
        $i = 1;

        $ticketStatus = $this->listFilterValues(configValue('__TICKET_STATUS'));
        $ticketType = $this->listFilterValues(configValue('__TICKET_TYPE'));
        $departments = $this->filterByDepts();

        foreach ($ticketStatus as $status) {
            foreach ($ticketType as $type) {
                $this->appendMatchingTickets($tickets, $departments, $status, $type);
            }
        }

        foreach ($tickets as $kyTicket) {
            $maxTickets = configValue('__MAX_TICKETS');
            if (( $i <= $maxTickets ) || $maxTickets == -1) {
                $item = $match;

                $subject = $kyTicket->getSubject();
                if (strlen($subject) >= 59) {
                    $subject = substr($subject, 0, 56);
                    $subject .= "...";
                }

                $status = $kyTicket->getStatus()->getTitle();
                $class = "";
                if ($status == "Open") {
                    $class = "open";
                } elseif ($status == "In Progress") {
                    $class = "inProgress";
                } elseif ($status == "Closed") {
                    $class = "closed";
                } elseif ($status == "On Hold") {
                    $class = "onHold";
                }

                $item = preg_replace("/\\[class\\]/s", $class, $item);
                $item = preg_replace("/\\[ticketid\\]/s", $kyTicket->getId(), $item);
                $item = preg_replace("/\\[user\\]/s", $kyTicket->getFullName(), $item);
                $item = preg_replace("/\\[subject\\]/s", $subject, $item);
                $item = preg_replace("/\\[status\\]/s", $status, $item);


                $item = preg_replace("/\\[dept\\]/s", $kyTicket->getDepartment()->getTitle(), $item);
                $item = preg_replace("/\\[creation\\]/s", $kyTicket->getCreationTime(), $item);
                $item = preg_replace("/\\[type\\]/s", $kyTicket->getType()->getTitle(), $item);

                $itemlist .= (string) $item;

                $i++;
            }
        }
        return $itemlist;
    }
}
