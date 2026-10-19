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
            $departments = $this->filterResultSet($departments, "filterByTitle", $value);
        }

        return $departments;
    }

    /**
     * Kayako result sets expose filterBy* through __call. Call that method
     * directly so the return type stays a result set.
     *
     * @param mixed $argument
     */
    private function filterResultSet(\kyResultSet $set, string $method, $argument): \kyResultSet
    {
        return $set->__call($method, array($argument));
    }

    /**
     * @param array<int, object> $tickets
     * @param mixed $departments
     * @param mixed $status
     * @param mixed $type
     */
    private function appendMatchingTickets(array &$tickets, $departments, $status, $type): void
    {
        $filteredStatus = $this->filterResultSet(kyTicketStatus::getAll(), "filterByTitle", $status);
        $filteredType = $this->filterResultSet(kyTicketType::getAll(), "filterByTitle", $type);
        $ticketsearch = kyTicket::getAll($departments, $filteredStatus);
        $ticketsearch = $this->filterResultSet(
            $ticketsearch,
            "filterByTypeId",
            $filteredType->first()->getId()
        );

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

                $item = $this->replaceToken($item, "/\\[class\\]/s", $class);
                $item = $this->replaceToken($item, "/\\[ticketid\\]/s", $kyTicket->getId());
                $item = $this->replaceToken($item, "/\\[user\\]/s", $kyTicket->getFullName());
                $item = $this->replaceToken($item, "/\\[subject\\]/s", $subject);
                $item = $this->replaceToken($item, "/\\[status\\]/s", $status);
                $item = $this->replaceToken($item, "/\\[dept\\]/s", $kyTicket->getDepartment()->getTitle());
                $item = $this->replaceToken($item, "/\\[creation\\]/s", $kyTicket->getCreationTime());
                $item = $this->replaceToken($item, "/\\[type\\]/s", $kyTicket->getType()->getTitle());

                $itemlist .= $item;

                $i++;
            }
        }
        return $itemlist;
    }

    /**
     * @param non-empty-string $pattern
     * @param mixed $replacement
     */
    private function replaceToken(string $subject, string $pattern, $replacement): string
    {
        if (!is_scalar($replacement)) {
            $replacement = "";
        }

        $updated = preg_replace($pattern, (string) $replacement, $subject);
        if (!is_string($updated)) {
            return $subject;
        }

        return $updated;
    }
}
