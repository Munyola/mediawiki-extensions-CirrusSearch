/*jshint esversion: 6,  node:true */

const defineSupportCode = require('cucumber').defineSupportCode,
	SearchResultsPage = require('../support/pages/search_results_page'),
	ArticlePage = require('../support/pages/article_page'),
	expect = require( 'chai' ).expect;

defineSupportCode( function( {Then,When} ) {
	When( /^I go search for (.*)$/, function ( title ) {
		return this.visit( SearchResultsPage.search( title ) );
	} );

	Then( /^there are no search results/, function() {
		expect(SearchResultsPage.has_search_results(), 'there are no search results').to.equal(false);
	} );

	When( /^I search for (.*)$/, function( search ) {
		// If on the SRP already use the main search
		if ( SearchResultsPage.is_on_srp() ) {
			SearchResultsPage.search_query = search;
			SearchResultsPage.click_search();
		} else {
			ArticlePage.search_query_top_right = search;
			ArticlePage.click_search_top_right();
		}
	} );

	Then( /^there is (no|a) link to create a new page from the search result$/, function (no_or_a) {
		let msg = `there is ${no_or_a} link to create a new page from the search result`;
		expect(SearchResultsPage.has_create_page_link(), msg).to.equal( no_or_a !== 'no' );
	} );

	Then( /^there is no warning$/, function () {
		let msg = 'there is no warning';
		expect(SearchResultsPage.get_warnings(), msg).to.have.lengthOf(0);
	} );

	Then( /^(.*) is the first search result$/, function (result) {
		let msg = `${result} is the first search result`;
		expect(SearchResultsPage.is_on_srp(), msg).to.equal(true);
		expect(SearchResultsPage.has_search_results(), msg).to.equal(true);
		expect(SearchResultsPage.get_result_at(1), msg).to.equal( result );
	} );
});
