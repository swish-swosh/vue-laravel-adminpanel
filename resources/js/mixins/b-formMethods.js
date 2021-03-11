export default {
    methods: {
    // ** FILE UPLOAD AND PREVIEW
      // uploadItem MUST be an object ( needs to be by reference)
      // default:
      // uploadItem.filePreview = null
      // uploadItem.fileUpload = null
      // ----
      fileUploadAndPreview(e, uploadItem) {

        // if user cancels upload, clear upload
        if(typeof e.target.files[0] == 'undefined'){
          uploadItem.filePreview = null
          uploadItem.fileUpload = null
          return
        }

        // form field fileUpload field (uploaded item).
        uploadItem.fileUpload = e.target.files[0]
        const reader = new FileReader()
        reader.readAsDataURL(e.target.files[0])
        reader.onload = e =>{
          // fileUpload preview field (what is previewed).
          uploadItem.filePreview = e.target.result
        }
      },
      // cancel the uploads, remove items
      fileUploadAndPreviewCancel(uploadItem) {
          uploadItem.filePreview = null
          uploadItem.fileUpload = null
      },
      // transform form json object to formData object which can upload files, hold strings
      // or stingified objects and arrays
      getFormData(object) {
        const formData = new FormData()
        Object.keys(object).forEach(
          key => object[key] instanceof File || typeof object[key] !== 'object' ?
            formData.append(key, object[key]): // store files
            formData.append(key, JSON.stringify(object[key])) // store objects
        )
        return formData
      },

      filesToFormData(files) {
        const formData = new FormData()
        let n = 0
        files.forEach(file => formData.append(
            'file'+(n++), JSON.stringify(file)
          )
        )
        return formData
      }
    }
}

// typeof object[key] == 'object'