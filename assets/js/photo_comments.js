import Request from 'superagent'
import createRequest from './request_headers'

let commentsWrapper = document.getElementsByClassName('js-comments-list')[0]
let commentForm = document.getElementsByClassName('js-comment-form')[0]

let ajaxCommentSubmit = (form) => {
	form.target = 'hiddenFrame'

	form.addEventListener('submit', (event) => {
		event.preventDefault()

		let photoId = form.dataset['photo-id']
		let url = `/photos/${photoId}/comments`

		createRequest(Request.post(url))
			.send(new FormData(form))
			.then( (response) => {
				response = JSON.parse(response.text)
				addComment(response.comment)
				clearCommentForm()
				console.log(response)
			})
			.catch( (err) => {
				console.log(err)
			})
	})
}

let addComment = (comment) => {
	let newComment = document.createElement('p')
	newComment.innerHTML = `${comment.username} said: ${comment.text}`
	commentsWrapper.appendChild(newComment)
}

let clearCommentForm = () => {
	commentForm.reset()
}

if (commentForm) {
	ajaxCommentSubmit(commentForm)
}
