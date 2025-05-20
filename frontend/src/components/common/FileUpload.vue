<template>
  <div class="file-upload">
    <label class="upload-label">
      <input 
        type="file" 
        @change="handleFileChange"
        accept=".pdf,.doc,.docx"
        style="display: none;"
      >
      <span class="btn btn-outline-primary">Choose File</span>
      <span class="file-name">{{ fileName }}</span>
    </label>
    <div v-if="currentFile" class="current-file">
      Current file: <a :href="currentFile" target="_blank">{{ currentFileName }}</a>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  props: {
    modelValue: File,
    currentFile: String
  },
  setup(props, { emit }) {
    const fileName = ref('')
    
    const currentFileName = computed(() => {
      if (!props.currentFile) return ''
      return props.currentFile.split('/').pop()
    })

    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        fileName.value = file.name
        emit('update:modelValue', file)
      }
    }

    return {
      fileName,
      currentFileName,
      handleFileChange
    }
  }
}
</script>

<style scoped>
.file-upload {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.upload-label {
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
}

.file-name {
  color: #666;
}

.current-file a {
  color: #1967d2;
  text-decoration: none;
}
</style>