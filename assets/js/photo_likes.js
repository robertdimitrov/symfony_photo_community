import Request from 'superagent'
import createRequest from './request_headers'

let photoLikeButtons = document.getElementsByClassName('js-photo-like')
let photoUnlikeButtons = document.getElementsByClassName('js-photo-unlike')

let ajaxPhotoLike = (button) => {
	button.addEventListener('click', (event) => {
		let photoId = button.dataset['photo-id']
		let url = `/photos/${photoId}/likes`

		createRequest(Request.post(url))
			.then( (response) => {
				response = JSON.parse(response.text)
				if (response.status === 'success') {
					let unlikeButton = document.querySelector(`.js-photo-unlike[data-photo-id="${photoId}"]`)
					unlikeButton.setAttribute('data-like-id', response.likeId)
					toggleButtons(button)
				}
			}).catch( (err) => {
				console.log(err)
			})
	})
}

let ajaxPhotoUnlike = (button) => {
	button.addEventListener('click', (event) => {
		let photoId = button.dataset['photo-id']
		let likeId = button.dataset['like-id']
		let url = `/photos/${photoId}/likes/${likeId}`

		createRequest(Request.delete(url))
			.then( (response) => {
				response = JSON.parse(response.text)
				if (response.status === 'success') {
					button.removeAttribute('data-like-id')
					toggleButtons(button)
				}
			}).catch( (err) => {
				console.log(err)
			})
	})
}

let toggleButtons = (clickedButton) => {
	let otherButtonClass = '.js-photo-unlike'
	if (clickedButton.classList.contains('js-photo-unlike')) {
		otherButtonClass = '.js-photo-like'
	}

	let otherButton = document.querySelector(`${otherButtonClass}[data-photo-id="${clickedButton.dataset['photo-id']}"]`)

	if (clickedButton.classList.contains('hidden')) {
		clickedButton.classList.remove('hidden')
		otherButton.classList.add('hidden')
	} else {
		clickedButton.classList.add('hidden')
		otherButton.classList.remove('hidden')
	}
}

for (let i = 0; i < photoLikeButtons.length; i++) {
	ajaxPhotoLike(photoLikeButtons[i])
}

for (let i = 0; i < photoUnlikeButtons.length; i++) {
	ajaxPhotoUnlike(photoUnlikeButtons[i])
}