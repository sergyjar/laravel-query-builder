<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Sort & Order
	|--------------------------------------------------------------------------
	|
	| sort_key и order_key - keys that will be regarded as field keys for sorting and sorting direction, respectively.
	| These keys can be redefined if you need to use different names of query parameters to designate fields
	| and sorting direction.
	|
	| sort_default и order_default - default sort values and sort direction.
	| If there is a request to set sorting for the request, but no values are passed, then the field data will be used.
	*/

	'sort_key' => 'sort',
	'order_key' => 'order',

	'sort_default' => 'id',
	'order_default' => 'asc',

	/*
	|--------------------------------------------------------------------------
	| Page & PerPage
	|--------------------------------------------------------------------------
	|
	| page и per_page - names of fields that will be regarded as fields for pagination.
	| "page" will be used to get the list page, and "perPage" will be used to get a certain number of entries per page.
	| These fields can be overridden if you need to use different query parameter names to indicate the page
	| and the number of records per page.
	|
	| page_default и per_page_default - default page values and number of records per pagination page.
	| If there is a request to set pagination for the request, but no values are passed,
	| then the field data will be used.
	*/

	'page' => 'page',
	'per_page' => 'perPage',

	'page_default' => 1,
	'per_page_default' => 20,
];
