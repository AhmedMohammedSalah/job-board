<template>
    <div class="comments-section mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="mb-0 fw-bold text-primary">Comments</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <form @submit.prevent="addComment" v-if="isAuthenticated">
                        <div class="form-group mb-2">
                            <textarea v-model="newComment" class="form-control" rows="3" placeholder="Add a comment..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                    <div v-else class="alert alert-info border-0 shadow-sm">
                        Please
                        <router-link :to="{ name: 'Login' }" class="alert-link">login</router-link>
                        to post a comment.
                    </div>
                </div>

                <div v-if="loadingComments" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="commentsError" class="alert alert-danger border-0 shadow-sm">
                    {{ commentsError }}
                </div>

                <div v-else>
                    <div v-if="comments.length === 0" class="text-muted py-3 text-center">
                        No comments yet. Be the first to comment!
                    </div>

                    <div v-for="comment in comments" :key="comment?.id"
                        class="comment mb-4 p-3 rounded bg-light shadow-sm">
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

                                    <button v-show="canEditOrDelete(comment)" @click="editComment(comment)"
                                        class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </button>

                                    <button v-show="canEditOrDelete(comment)" @click="deleteComment(comment.id)"
                                        class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </div>

                                <div v-show="activeReply === comment.id" class="mb-3 p-3 bg-white rounded shadow-sm">
                                    <form @submit.prevent="addReply(comment.id)">
                                        <div class="form-group mb-2">
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

                                <div v-show="editingComment === comment.id" class="mb-3 p-3 bg-white rounded shadow-sm">
                                    <form @submit.prevent="updateComment(comment.id, editContent)">
                                        <div class="form-group mb-2">
                                            <textarea v-model="editContent" class="form-control" rows="3"
                                                required></textarea>
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

                                <RepliesSection :replies="comment.replies" :localUser="localUser"
                                    @edit-reply="editReply" @delete-reply="deleteReply"
                                    :editingCommentId="editingComment" @update-reply="handleUpdateReply" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="successMessage" class="alert alert-success mt-3 animated-alert">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="alert alert-danger mt-3 animated-alert">
            {{ errorMessage }}
        </div>
        <div v-if="infoMessage" class="alert alert-info mt-3 animated-alert">
            {{ infoMessage }}
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
            localUser: null,
            successMessage: '',
            errorMessage: '',
            infoMessage: ''
        }
    },
    created() {
        this.loadLocalUser();
        this.fetchComments();
    },
    methods: {
        loadLocalUser() {
            try {
                const user = localStorage.getItem("user");
                if (user) {
                    this.localUser = JSON.parse(user);
                } else {
                    this.localUser = null;
                }
            } catch (e) {
                this.localUser = null;
            }
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },

        canEditOrDelete(comment) {
            const currentUserId = this.localUser ? Number(this.localUser.id) : null;
            const commentUserId = comment.user_id ? Number(comment.user_id) : null;
            return this.isAuthenticated && currentUserId !== null && commentUserId === currentUserId;
        },

        async fetchComments() {
    this.loadingComments = true;
    this.commentsError = null;
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/jobs/${this.jobId}/comments`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            }
        });
        
        // Ensure comments have replies array and proper structure
        this.comments = response.data.map(comment => ({
            ...comment,
            replies: comment.replies || [],
            user: comment.user || { id: null, name: 'Unknown', avatar: null }
        }));
        
    } catch (error) {
        this.commentsError = error.response?.data?.message || "Failed to load comments";
        console.error("Error loading comments:", error);
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

                // Instead of unshifting directly, refresh the comments
                await this.fetchComments();
                this.newComment = '';
                this.showNotification('Comment posted successfully!', 'success');
            } catch (error) {
                this.showNotification(error.response?.data?.message || "Failed to post comment", 'danger');
                if (error.response?.status === 401) {
                    this.$emit('auth-failed');
                }
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
                    if (!parentComment.replies) {
                        parentComment.replies = [];
                    }
                    parentComment.replies.push(response.data.comment);
                }

                this.replyContent = '';
                this.activeReply = null;
                this.showNotification('Reply posted successfully!', 'success');
            } catch (error) {
                this.showNotification(error.response?.data?.message || "Failed to post reply", 'danger');
                if (error.response?.status === 401) {
                    this.$emit('auth-failed');
                }
            }
        },

        editComment(comment) {
            if (!this.canEditOrDelete(comment)) {
                this.showNotification('You are not authorized to edit this comment.', 'info');
                return;
            }
            this.editingComment = comment.id;
            this.editContent = comment.content;
            this.activeReply = null;
        },

        editReply(reply) {
            this.editingComment = reply.id;
            this.editContent = reply.content;
            this.activeReply = null;
        },

        async updateComment(commentId, newContent) {
            try {
                await axios.put(`http://127.0.0.1:8000/api/comments/${commentId}`, {
                    content: newContent
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    }
                });

                const comment = this.findCommentById(commentId);
                if (comment) {
                    comment.content = newContent;
                    comment.updated_at = new Date().toISOString();
                }

                this.editingComment = null;
                this.editContent = '';
                this.showNotification('Updated successfully!', 'success');
            } catch (error) {
                this.showNotification(error.response?.data?.message || "Failed to update", 'danger');
                if (error.response?.status === 401) {
                    this.$emit('auth-failed');
                }
            }
        },

        handleUpdateReply(replyId, newContent) {
            this.updateComment(replyId, newContent);
        },

        async deleteComment(commentId) {
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
                this.showNotification('Deleted successfully!', 'success');
            } catch (error) {
                this.showNotification(error.response?.data?.message || "Failed to deleted", 'danger');
                console.error("Failed to delete comment", error);
            }
        },

        async deleteReply(replyId) {
            await this.deleteComment(replyId);
        },

        toggleReply(commentId) {
            if (!this.isAuthenticated) {
                this.showNotification('Please login to post a reply.', 'info');
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
                if (c.replies) {
                    const reply = c.replies.find(r => r.id === id);
                    if (reply) return reply;
                }
            }
            return null;
        },
        showNotification(message, type) {
            this.successMessage = '';
            this.errorMessage = '';
            this.infoMessage = '';

            if (type === 'success') {
                this.successMessage = message;
            } else if (type === 'danger') {
                this.errorMessage = message;
            } else if (type === 'info') {
                this.infoMessage = message;
            }

            setTimeout(() => {
                this.successMessage = '';
                this.errorMessage = '';
                this.infoMessage = '';
            }, 3000);
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
    transition: all 0.3s ease-in-out;
}

.comment:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
}

.animated-alert {
    animation: fadeInOut 3s forwards;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    min-width: 250px;
    text-align: center;
    padding: 10px 15px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }

    20% {
        opacity: 1;
        transform: translateY(0);
    }

    80% {
        opacity: 1;
        transform: translateY(0);
    }

    100% {
        opacity: 0;
        transform: translateY(-20px);
    }
}
</style>