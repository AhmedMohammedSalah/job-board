<template>
    <div class="container text-center my-5">
        <h1>Thank you for applying!</h1>
        <p>Your application for {{ jobTitle }} has been submitted.</p>
        <router-link :to="{ name: 'JobDetails', params: { id: $route.query.jobId } }" class="btn btn-primary mt-3">
            Back to Job
        </router-link>
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