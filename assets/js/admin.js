import Request from 'superagent'
import createRequest from './request_headers'

let photoApproveButtons = document.getElementsByClassName('js-photo-approve')
let photoDenyButtons = document.getElementsByClassName('js-photo-deny')

let ajaxAdminPhotoAction = (button, status) => {
	button.addEventListener('click', (event) => {
		if (status == 'denied') {
			if (!confirm('Are you sure you want to deny this photo?')) {
				return
			}
		}

		let photoId = button.dataset['photo-id']
		let url = `/photos/${photoId}/admin?status=${status}`

		createRequest(Request.post(url))
			.then( (response) => {
				response = JSON.parse(response.text)
				if (response.status === 'success') {
					let photoWrapper = document.querySelector(`.js-photo-wrapper[data-photo-id="${photoId}"]`)
					photoWrapper.style.display = 'none'
				}
			}).catch( (err) => {
				console.log(err)
			})
	})
}

for (let i = 0; i < photoApproveButtons.length; i++) {
	ajaxAdminPhotoAction(photoApproveButtons[i], 'approved')
}

for (let i = 0; i < photoDenyButtons.length; i++) {
	ajaxAdminPhotoAction(photoDenyButtons[i], 'denied')
}