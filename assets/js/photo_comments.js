import Request from 'superagent'
import createRequest from './request_headers'

let commentForm = document.getElementsByClassName('comment-form')[0]

let ajaxCommentSubmit = (form) => {
	form.target = 'hiddenFrame'

	form.addEventListener('submit', (event) => {
		event.preventDefault()

		let photoId = form.dataset['photo-id']
		let url = `/photos/${photoId}/comments`

		createRequest(Request.post(url))
			.send(new FormData(form))
			.then( (response) => {
				console.log(response)
			})
			.catch( (err) => {
				console.log(err)
			})
	})
}

ajaxCommentSubmit(commentForm)