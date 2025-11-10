/**
 * Card Management JavaScript
 * Handles card creation, editing, deletion, and comments
 */

class CardManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        this.currentCardId = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Card form submission
        const createCardForm = document.getElementById('createCardForm');
        if (createCardForm) {
            createCardForm.addEventListener('submit', (e) => this.handleCreateCard(e));
        }

        const editCardForm = document.getElementById('editCardForm');
        if (editCardForm) {
            editCardForm.addEventListener('submit', (e) => this.handleEditCard(e));
        }

        const addCommentForm = document.getElementById('addCommentForm');
        if (addCommentForm) {
            addCommentForm.addEventListener('submit', (e) => this.handleAddComment(e));
        }

        // Modal background click handlers
        this.setupModalClickHandlers();
    }

    setupModalClickHandlers() {
        const modals = ['createCardModal', 'editCardModal', 'commentsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target.id === modalId) {
                        this.closeModal(modalId);
                    }
                });
            }
        });
    }

    // Modal Management
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Ensure proper centering
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            
            // Animate modal entrance
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
                
                // Reset forms
                const form = modal.querySelector('form');
                if (form) {
                    form.reset();
                }
            }, 200);
        }
    }

    // Card Operations
    openCreateCardModal() {
        this.loadProjectMembers();
        this.openModal('createCardModal');
    }

    closeCreateCardModal() {
        this.closeModal('createCardModal');
    }

    async openEditCardModal(cardId) {
        this.currentCardId = cardId;
        await this.loadCardData(cardId);
        await this.loadProjectMembersForEdit();
        this.openModal('editCardModal');
    }

    closeEditCardModal() {
        this.closeModal('editCardModal');
    }

    async openCommentsModal(cardId) {
        this.currentCardId = cardId;
        document.getElementById('comment_card_id').value = cardId;
        await this.loadCardComments(cardId);
        await this.loadCardTitle(cardId);
        this.openModal('commentsModal');
    }

    closeCommentsModal() {
        this.closeModal('commentsModal');
    }

    async loadCardData(cardId) {
        try {
            const response = await fetch(`/cards/${cardId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                const card = data.data.card;
                
                // Populate edit form
                document.getElementById('edit_card_id').value = card.id;
                document.getElementById('edit_card_title').value = card.card_title;
                document.getElementById('edit_card_description').value = card.description || '';
                document.getElementById('edit_card_priority').value = card.priority || '';
                document.getElementById('edit_estimated_hours').value = card.estimated_hours || '';
                document.getElementById('edit_card_status').value = card.status;
                document.getElementById('edit_due_date').value = card.due_date ? card.due_date.split('T')[0] : '';
                
                // Set assigned user in the new search system
                // Check both assignedUser and assigned_user properties
                const assignedUser = card.assignedUser || card.assigned_user;
                if (assignedUser && assignedUser.id) {
                    const editSelectedUserId = document.getElementById('editSelectedUserId');
                    const editSelectedUserDisplay = document.getElementById('editSelectedUserDisplay');
                    const editUserSearch = document.getElementById('editUserSearch');
                    
                    if (editSelectedUserId && editSelectedUserDisplay && editUserSearch) {
                        editSelectedUserId.value = assignedUser.id;
                        document.getElementById('editSelectedUserName').textContent = assignedUser.name;
                        document.getElementById('editSelectedUserEmail').textContent = assignedUser.email;
                        document.getElementById('editSelectedUserInitial').textContent = assignedUser.name.charAt(0).toUpperCase();
                        
                        if (assignedUser.avatar) {
                            document.getElementById('editSelectedUserAvatar').src = assignedUser.avatar;
                            document.getElementById('editSelectedUserAvatar').classList.remove('hidden');
                            document.getElementById('editSelectedUserInitialContainer').style.display = 'none';
                        } else {
                            document.getElementById('editSelectedUserAvatar').classList.add('hidden');
                            document.getElementById('editSelectedUserInitialContainer').style.display = 'flex';
                        }
                        
                        editSelectedUserDisplay.classList.remove('hidden');
                        editUserSearch.value = assignedUser.name;
                    }
                }
            } else {
                this.showError('Failed to load card data');
            }
        } catch (error) {
            console.error('Failed to load card data:', error);
            this.showError('Failed to load card data');
        }
    }

    async loadCardTitle(cardId) {
        try {
            const response = await fetch(`/cards/${cardId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                document.getElementById('commentsTaskTitle').textContent = data.data.card.card_title;
            }
        } catch (error) {
            console.error('Failed to load card title:', error);
        }
    }

    async loadProjectMembers() {
        try {
            const response = await fetch('/project-members', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                const select = document.getElementById('assigned_user');
                select.innerHTML = '<option value="">Select team member...</option>';
                
                data.data.members.forEach(member => {
                    const option = document.createElement('option');
                    option.value = member.user.id;
                    option.textContent = member.user.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load project members:', error);
        }
    }

    async loadProjectMembersForEdit() {
        try {
            const response = await fetch('/project-members', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                const select = document.getElementById('edit_assigned_user');
                select.innerHTML = '<option value="">Select team member...</option>';
                
                data.data.members.forEach(member => {
                    const option = document.createElement('option');
                    option.value = member.user.id;
                    option.textContent = member.user.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load project members:', error);
        }
    }

    async loadCardComments(cardId) {
        try {
            const response = await fetch(`/cards/${cardId}/comments`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                const commentsList = document.getElementById('commentsList');
                
                if (data.data.comments.length === 0) {
                    commentsList.innerHTML = `
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">No comments yet</h3>
                            <p class="text-gray-600">Be the first to comment on this task!</p>
                        </div>
                    `;
                } else {
                    commentsList.innerHTML = data.data.comments.map(comment => `
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">${comment.user.name.charAt(0).toUpperCase()}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="font-semibold text-gray-800 text-sm">${comment.user.name}</span>
                                        <span class="text-gray-500 text-xs">${new Date(comment.created_at).toLocaleDateString()}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm leading-relaxed">${comment.comment_text}</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            }
        } catch (error) {
            console.error('Failed to load comments:', error);
        }
    }

    async handleCreateCard(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        // Get user_id from the hidden field instead of select
        const selectedUserId = document.getElementById('selectedUserId').value;
        if (selectedUserId) {
            formData.set('user_id', selectedUserId);
        }
        
        try {
            const response = await fetch('/cards', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.showSuccess('Card created successfully!');
                this.closeCreateCardModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showError(result.message || 'Failed to create card');
            }
        } catch (error) {
            this.showError('Failed to create card. Please try again.');
        }
    }

    async handleEditCard(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const cardId = formData.get('card_id');
        
        // Get user_id from the hidden field instead of select
        const selectedUserId = document.getElementById('editSelectedUserId').value;
        if (selectedUserId) {
            formData.set('user_id', selectedUserId);
        }
        
        // Convert FormData to regular object for JSON
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (value !== '') { // Only include non-empty values
                data[key] = value;
            }
        }
        
        // Remove card_id from data since it's in the URL
        delete data.card_id;
        
        try {
            const response = await fetch(`/cards/${cardId}`, {
                method: 'PUT',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.showSuccess('Card updated successfully!');
                this.closeEditCardModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showError(result.message || 'Failed to update card');
            }
        } catch (error) {
            console.error('Edit card error:', error);
            this.showError('Failed to update card. Please try again.');
        }
    }

    async handleAddComment(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const cardId = formData.get('card_id');
        
        try {
            const response = await fetch(`/cards/${cardId}/comments`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('comment_text').value = '';
                await this.loadCardComments(cardId);
                this.showSuccess('Comment added successfully!');
            } else {
                this.showError(result.message || 'Failed to add comment');
            }
        } catch (error) {
            this.showError('Failed to add comment. Please try again.');
        }
    }

    async deleteCard(cardId) {
        const result = await Swal.fire({
            title: 'Delete Task?',
            text: 'This action cannot be undone. The task and all its data will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-white/20'
            }
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/cards/${cardId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    this.showSuccess('Card deleted successfully!');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showError(data.message || 'Failed to delete card');
                }
            } catch (error) {
                this.showError('Failed to delete card. Please try again.');
            }
        }
    }

    // Utility Functions
    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            showConfirmButton: false,
            timer: 1500,
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-white/20'
            }
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-white/20'
            }
        });
    }
}

// Global functions for card management
function openCreateCardModal() {
    window.cardManager.openCreateCardModal();
}

function closeCreateCardModal() {
    window.cardManager.closeCreateCardModal();
}

function openEditCardModal(cardId) {
    window.cardManager.openEditCardModal(cardId);
}

function closeEditCardModal() {
    window.cardManager.closeEditCardModal();
}

function openCommentsModal(cardId) {
    window.cardManager.openCommentsModal(cardId);
}

function closeCommentsModal() {
    window.cardManager.closeCommentsModal();
}

function deleteCard(cardId) {
    window.cardManager.deleteCard(cardId);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.cardManager = new CardManager();
});
