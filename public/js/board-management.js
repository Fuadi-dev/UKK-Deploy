/**
 * Board Management JavaScript
 * Handles board and card creation, editing, and deletion
 */

class BoardManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Board form submission
        const createBoardForm = document.getElementById('createBoardForm');
        if (createBoardForm) {
            createBoardForm.addEventListener('submit', (e) => this.handleCreateBoard(e));
        }

        const editBoardForm = document.getElementById('editBoardForm');
        if (editBoardForm) {
            editBoardForm.addEventListener('submit', (e) => this.handleEditBoard(e));
        }

        // Card form submission
        const createCardForm = document.getElementById('createCardForm');
        if (createCardForm) {
            createCardForm.addEventListener('submit', (e) => this.handleCreateCard(e));
        }

        // Modal background click handlers
        this.setupModalClickHandlers();
    }

    setupModalClickHandlers() {
        const modals = ['createBoardModal', 'editBoardModal', 'createCardModal'];
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

    // Board Operations
    openCreateBoardModal() {
        this.openModal('createBoardModal');
    }

    closeCreateBoardModal() {
        this.closeModal('createBoardModal');
    }

    async handleCreateBoard(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('/boards', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.showSuccess('Board created successfully!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showError(result.message || 'Failed to create board');
            }
        } catch (error) {
            this.showError('Failed to create board. Please try again.');
        }
    }

    editBoard(boardId, boardName, description) {
        document.getElementById('edit_board_id').value = boardId;
        document.getElementById('edit_board_name').value = boardName;
        document.getElementById('edit_board_description').value = description || '';
        this.openModal('editBoardModal');
    }

    closeEditBoardModal() {
        this.closeModal('editBoardModal');
    }

    async handleEditBoard(e) {
        e.preventDefault();
        
        const boardId = document.getElementById('edit_board_id').value;
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch(`/boards/${boardId}`, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.showSuccess('Board updated successfully!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showError(result.message || 'Failed to update board');
            }
        } catch (error) {
            this.showError('Failed to update board. Please try again.');
        }
    }

    async deleteBoard(boardId) {
        const confirmed = await this.showConfirmation(
            'Are you sure?',
            "You won't be able to revert this! All cards in this board will be deleted.",
            'Yes, delete it!'
        );

        if (confirmed) {
            try {
                const response = await fetch(`/boards/${boardId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    this.showSuccess('Board deleted successfully!');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showError('Failed to delete board');
                }
            } catch (error) {
                this.showError('Failed to delete board. Please try again.');
            }
        }
    }

    // Card Operations
    openCreateCardModal(boardId) {
        document.getElementById('card_board_id').value = boardId;
        this.loadProjectMembers();
        this.openModal('createCardModal');
    }

    closeCreateCardModal() {
        this.closeModal('createCardModal');
    }

    async loadProjectMembers() {
        try {
            const response = await fetch('/api/project-members', {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                const select = document.getElementById('assigned_user');
                select.innerHTML = '<option value="">Select team member...</option>';
                
                if (data.data && data.data.members) {
                    data.data.members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.user.id;
                        option.textContent = `${member.user.name} (${member.user.role})`;
                        
                        if (member.user.status === 'working') {
                            option.textContent += ' - Working';
                            option.disabled = true;
                        }
                        
                        select.appendChild(option);
                    });
                }
            }
        } catch (error) {
            console.error('Error loading project members:', error);
        }
    }

    async handleCreateCard(e) {
        e.preventDefault();
        
        const boardId = document.getElementById('card_board_id').value;
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch(`/boards/${boardId}/cards`, {
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
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showError(result.message || 'Failed to create card');
            }
        } catch (error) {
            this.showError('Failed to create card. Please try again.');
        }
    }

    // Utility Methods
    showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert('Success: ' + message);
        }
    }

    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
            });
        } else {
            alert('Error: ' + message);
        }
    }

    async showConfirmation(title, text, confirmButtonText) {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText
            });
            return result.isConfirmed;
        } else {
            return confirm(title + '\n\n' + text);
        }
    }
}

// Initialize BoardManager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.boardManager = new BoardManager();
});

// Global functions for backward compatibility
function openCreateBoardModal() {
    if (window.boardManager) {
        window.boardManager.openCreateBoardModal();
    }
}

function closeCreateBoardModal() {
    if (window.boardManager) {
        window.boardManager.closeCreateBoardModal();
    }
}

function editBoard(boardId, boardName, description) {
    if (window.boardManager) {
        window.boardManager.editBoard(boardId, boardName, description);
    }
}

function closeEditBoardModal() {
    if (window.boardManager) {
        window.boardManager.closeEditBoardModal();
    }
}

function openCreateCardModal(boardId) {
    if (window.boardManager) {
        window.boardManager.openCreateCardModal(boardId);
    }
}

function closeCreateCardModal() {
    if (window.boardManager) {
        window.boardManager.closeCreateCardModal();
    }
}

function deleteBoard(boardId) {
    if (window.boardManager) {
        window.boardManager.deleteBoard(boardId);
    }
}
