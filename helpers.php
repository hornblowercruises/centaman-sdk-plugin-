<?php
/**
 * Attempts to authenticate an existing member with their Member ID and Last Name, or Email and Password
 *
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function centaman_member_authenticate( $args ) {
	$members = new \Zao\ZCSDK\Services\Member_Services();

	$auth = $members->authenticate( $args );

	return $auth->is_authenticated();
}

/**
 * Returns all timed ticket booking types.
 * If a Booking ID is passed, returns tickets for just that type.
 *
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function centaman_get_timed_ticket_types( $booking_id = null ) {
	return ( new Zao\ZCSDK\Services\Ticket_Services() )->get_tickets( $booking_id );
}

/**
 * Creates a customer Contact record.
 * Expects an array with First Name, Last Name, Email and Address.
 *
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function centaman_create_customer( $args ) {
	return ( new \Zao\ZCSDK\Services\Ticket_Services() )->create_contact( $args );
}

function centaman_get_timed_ticket_booking_types( $booking_type_id = 0, $start = '', $end = '' ) {
	return ( new \Zao\ZCSDK\Services\Ticket_Services() )->get_timed_ticket_types( $booking_type_id, $start, $end );
}


function centaman_get_remaining_event_capacity( $timed_ticket_type_id, $start_date ) {
	return ( new \Zao\ZCSDK\Services\Ticket_Services() )->get_capacity( $timed_ticket_type_id, $start_date );
}

/**
 * Creates transaction in Centaman
 *
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function centaman_create_transaction( $args ) {

	// Example $args expected to be passed:
	$_args = array(
		'TimedTicketTypeId'          => $args['timed_ticket_type_id'], //  This is internal id for the TimedTicketType(Primary Booking)
		'TimedTicketTypeDescription' => $args['timed_ticket_type_description'], // The description of the TimedTicketType
		'BookingTypeId'              => $args['booking_type_id'],
		'StartDate'                  => $args['booking_date'],
		'StartTime'                  => $args['booking_start_time'],
		'EndTime'                    => $args['booking_end_time'],
		'PaymentReference'           => $args['transaction_id'], // Should pass through from global Payments
		'BookingCost'                => $args['total'],
		'TotalPaid'                  => $args['total'],
		'BookingContactId'           => $args['member_code'], //Should get as part of create_contact() response
		'TotalTickets'               => $args['total_tickets'],
		'Item'                       => array(
			'ItemDescription' => $item['ticket_description'], // TicketDescription returned from GET ticket_services/TimedTicket?TimedTicketTypeId={TimedTicketTypeId}.
			'ItemCode'        => $item['ticket_id'], // TicketId of the Ticket
			'Quantity'        => $item['count'],
			'ItemCost'        => $item['ticket_price'],
			'TotalPaid'       => $item['line_total'],
		)
	);

	return ( new \Zao\ZCSDK\Services\Ticket_Services() )->create_transaction( $args );
}

/**
 * This function is called to temporarily reserve tickets while the customer completes
 * the transaction.
 *
 * Tickets that have been reserved reduce the Vacancy count for other web
 * sessions as well as desktop Point Of Sale.
 *
 * The temporary reservation is removed when the customer completes purchase and the reservation
 * becomes permanent or when they end the browser session
 *
 * @param array $args Expects an array of arrays with the following parameters:
 *                    Example:
 * array(
 *    array(
 *        // TimedTicketTypeId: (int, Required) TimedTicketType Id (Centaman Internal Id).
 *        'TimedTicketTypeId' => '2',
 *
 *        // TimedTicketId: (int, Required) Timed Ticket Id.
 *        'TimedTicketId' => '2',
 *
 *        // NumberOfTickets: (int, Required) Number of spots reserving for TimedTicket.
 *        'NumberOfTickets' => '2',
 *    ),
 * )
 * @return mixed
 */
function centaman_hold_spot( $args ) {
	return ( new \Zao\ZCSDK\Services\Ticket_Services() )->hold_spot( $args );
}

/**
 * Get the last api request instance, to use it's methods.
 *
 * @return null|\Zao\ZCSDK\Services\API_Request
 */
function centaman_get_last_api_request_instance() {
	return \Zao\ZCSDK\Services\API_Request::get_last_instance();
}
