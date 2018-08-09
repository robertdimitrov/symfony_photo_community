export default function createRequest(request){
	let csrfToken = document.querySelectorAll("meta[name=csrf-token]")[0].content

	request
		.set('X-CSRF-TOKEN', csrfToken)
		.set('Accept', 'application/json')

	return request
}