<?php

function getTicketTypes( $types )
{
	$wantedTypes = array();
	$knownTypes = kyTicketType::getAll();
	
	foreach( $knownTypes as $type )
	{
		foreach( $types as $wanted )
		{
			if( $type->getTitle() == $wanted )
			{
				$wantedTypes[] = $type; 
			}
		}
	}
	
	return $wantedTypes;
}

function getTicketStatuses( $statuses )
{
	$wantedStatuses = array();
	$knownStatuses = kyTicketStatus::getAll();
	
	foreach( $knownStatuses as $status )
	{
		foreach( $statuses as $wanted )
		{
			if( $status->getTitle() == $wanted )
			{
				$wantedStatuses[] = $status; 
			}
		}
	}
	
	return $wantedStatuses;
}