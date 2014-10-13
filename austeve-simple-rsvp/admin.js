
function addOption() {
	console.log("Adding option to RSVP form");

	jQuery('<tr/>', {
	    id: 'foo',
	    href: 'http://google.com',
	    title: 'Become a Googler',
	    rel: 'external',
	    text: 'Go to Google!'
	}).appendTo(#rsvp-options');
}