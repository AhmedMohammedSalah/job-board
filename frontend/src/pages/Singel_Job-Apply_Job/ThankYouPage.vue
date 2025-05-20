<template>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div class="container text-center bg-white p-5 rounded shadow-sm" style="max-width: 600px;">
            <h1 class="mb-4">Thank you for applying!</h1>
            <p class="fs-5 mb-4">Your application for <strong>{{ jobTitle }}</strong> has been submitted.</p>
            <router-link :to="{ name: 'JobDetails', params: { id: $route.query.jobId } }"
                class="btn btn-primary px-4 py-2">
                Back to Job
            </router-link>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            jobTitle: ''
        }
    },
    created() {
        this.jobTitle = this.$route.query.jobTitle || 'the position'
    }
}
</script>

<style scoped>
.min-vh-100 {
    min-height: 100vh;
}

.container {
    padding: 2rem;
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}
</style>