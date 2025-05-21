<template>
    <div class="comments-section mt-5">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0 fw-bold">Comments</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <form @submit.prevent="addComment" v-if="isAuthenticated">
                        <div class="form-group">
                            <textarea v-model="newComment" class="form-control" rows="3" placeholder="Add a comment..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
                    </form>
                    <div v-else class="alert alert-info">
                        Please <router-link :to="{ name: 'Login' }">login</router-link> to post a comment.
                    </div>
                </div>

                <div v-if="loadingComments" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="commentsError" class="alert alert-danger">
                    {{ commentsError }}
                </div>

                <div v-else>
                    <div v-if="comments.length === 0" class="text-muted py-3 text-center">
                        No comments yet. Be the first to comment!
                    </div>

                    <div v-for="comment in comments" :key="comment.id" class="comment mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <img :src="comment.user.avatar || '/images/default-avatar.png'" class="rounded-circle"
                                    width="40" height="40" alt="User avatar">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">{{ comment.user.name }}</h6>
                                    <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                                </div>
                                <p class="mb-2">{{ comment.content }}</p>

                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <button @click="toggleReply(comment.id)" class="btn btn-sm btn-outline-secondary">
                                        Reply
                                    </button>

                                    <button v-show="comment.user_id == currentUser?.id" @click="editComment(comment)"
                                        class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </button>

                                    <button v-show="comment.user_id == currentUser?.id"
                                        @click="deleteComment(comment.id)" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </div>

                                <div v-if="activeReply === comment.id" class="mb-3">
                                    <form @submit.prevent="addReply(comment.id)">
                                        <div class="form-group">
                                            <textarea v-model="replyContent" class="form-control" rows="2"
                                                placeholder="Write a reply..." required></textarea>
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Post Reply</button>
                                            <button @click="activeReply = null" type="button"
                                                class="btn btn-outline-secondary btn-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div v-show="localUser?.id == comment.user_id" class="mb-3">
                                    <form @submit.prevent="updateComment(comment.id)">
                                        <div class="form-group">
                                            <textarea :value="editContent" @input="editContent = $event.target.value"
                                                class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            <button @click="editingComment = null" type="button"
                                                class="btn btn-outline-secondary btn-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <RepliesSection :replies="comment.replies" :currentUser="currentUser"
                                    @edit-reply="editReply" @delete-reply="deleteReply" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import RepliesSection from './RepliesSection.vue';
import axios from 'axios';

export default {
    name: 'CommentsSection',
    components: {
        RepliesSection
    },
    props: {
        jobId: {
            type: [String, Number],
            required: true
        },
        currentUser: {
            type: Object,
            // default: null
        },
        isAuthenticated: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            comments: [],
            newComment: '',
            replyContent: '',
            activeReply: null,
            editingComment: null,
            editContent: '',
            loadingComments: false,
            commentsError: null,
            localUser: {
            }
           
        }
    },
    created() {
        this.fetchComments();
        // log the user in
        // console .log(this.props.currentUser);
    },
    mounted() {
        try {
            const user = localStorage.getItem("user");
            this.localUser = user ? JSON.parse(user) : { id: 1 };
        } catch (e) {
            this.localUser = { id: 1 };
        }
        console.log('Current User:', this.localUser.id);
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },
        // mounted
        
        async fetchComments() {
            this.loadingComments = true;
            this.commentsError = null;
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/jobs/${this.jobId}/comments`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });
                this.comments = response.data;
            } catch (error) {
                this.commentsError = error.response?.data?.message || "Failed to load comments";
            } finally {
                this.loadingComments = false;
            }
        },

        async addComment() {
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/comments', {
                    job_id: this.jobId,
                    content: this.newComment
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });

                this.comments.unshift(response.data.comment);
                this.newComment = '';
            } catch (error) {
                console.error("Failed to add comment", error);
                this.$emit('auth-failed');
            }
        },

        async addReply(commentId) {
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/comments', {
                    job_id: this.jobId,
                    parent_id: commentId,
                    content: this.replyContent
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });

                const parentComment = this.comments.find(c => c.id === commentId);
                if (parentComment) {
                    parentComment.replies.push(response.data.comment);
                }

                this.replyContent = '';
                this.activeReply = null;
            } catch (error) {
                console.error("Failed to add reply", error);
            }
        },

        editComment(comment) {
            this.editingComment = comment.id;
            this.editContent = comment.content;
        },

        editReply(reply) {
            this.editingComment = reply.id;
            this.editContent = reply.content;
        },

        async updateComment(commentId) {
            try {
                await axios.put(`http://127.0.0.1:8000/api/comments/${commentId}`, {
                    content: this.editContent
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });

                const comment = this.findCommentById(commentId);
                if (comment) {
                    comment.content = this.editContent;
                    comment.updated_at = new Date().toISOString();
                }

                this.editingComment = null;
                this.editContent = '';
            } catch (error) {
                console.error("Failed to update comment", error);
            }
        },

        async deleteComment(commentId) {
            if (!confirm('Are you sure you want to delete this comment?')) return;

            try {
                await axios.delete(`http://127.0.0.1:8000/api/comments/${commentId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });

                const commentIndex = this.comments.findIndex(c => c.id === commentId);
                if (commentIndex !== -1) {
                    this.comments.splice(commentIndex, 1);
                } else {
                    for (const comment of this.comments) {
                        const replyIndex = comment.replies.findIndex(r => r.id === commentId);
                        if (replyIndex !== -1) {
                            comment.replies.splice(replyIndex, 1);
                            break;
                        }
                    }
                }
            } catch (error) {
                console.error("Failed to delete comment", error);
            }
        },

        async deleteReply(replyId) {
            await this.deleteComment(replyId);
        },

        toggleReply(commentId) {
            if (!this.isAuthenticated) {
                return;
            }
            this.activeReply = this.activeReply === commentId ? null : commentId;
            this.replyContent = '';
            this.editingComment = null;
        },

        findCommentById(id) {
            const comment = this.comments.find(c => c.id === id);
            if (comment) return comment;

            for (const c of this.comments) {
                const reply = c.replies.find(r => r.id === id);
                if (reply) return reply;
            }

            return null;
        }
    }
}
</script>

<style scoped>
.comments-section {
    border-top: 1px solid #eee;
    padding-top: 2rem;
}

.comment {
    padding: 1rem;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.comment-actions {
    opacity: 0;
    transition: opacity 0.2s;
}

.comment:hover .comment-actions {
    opacity: 1;
}
</style>