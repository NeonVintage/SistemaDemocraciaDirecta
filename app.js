// Dashboard JavaScript

let currentUser = null;
let currentLawId = null;

// Check authentication on page load
window.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    loadLaws();
});

async function checkAuth() {
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'check_auth'
            })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            window.location.href = 'index.html';
            return;
        }
        
        currentUser = data.user;
        document.getElementById('user-name').textContent = `${data.user.nombre} ${data.user.apellido}`;
    } catch (error) {
        console.error('Error:', error);
        window.location.href = 'index.html';
    }
}

function logout() {
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'logout'
        })
    }).then(() => {
        window.location.href = 'index.html';
    });
}

function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(el => {
        el.classList.remove('active');
    });
    
    // Remove active class from all nav buttons
    document.querySelectorAll('.nav-btn').forEach(el => {
        el.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`${section}-section`).classList.add('active');
    
    // Add active class to clicked button
    event.target.closest('.nav-btn').classList.add('active');
    
    // Load data if needed
    if (section === 'propuestas' || section === 'aprobadas' || section === 'rechazadas') {
        loadLaws();
    }
}

async function loadLaws() {
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_laws'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayLaws(data.laws);
        }
    } catch (error) {
        console.error('Error loading laws:', error);
    }
}

function displayLaws(laws) {
    const propuestasList = document.getElementById('propuestas-list');
    const aprobadasList = document.getElementById('aprobadas-list');
    const rechazadasList = document.getElementById('rechazadas-list');
    
    propuestasList.innerHTML = '';
    aprobadasList.innerHTML = '';
    rechazadasList.innerHTML = '';
    
    const propuestas = laws.filter(law => law.status === 'propuesta');
    const aprobadas = laws.filter(law => law.status === 'aprobada');
    const rechazadas = laws.filter(law => law.status === 'rechazada');
    
    if (propuestas.length === 0) {
        propuestasList.innerHTML = '<p style="color: var(--text-secondary);">No hay leyes propuestas en este momento.</p>';
    } else {
        propuestas.forEach(law => {
            propuestasList.appendChild(createLawCard(law));
        });
    }
    
    if (aprobadas.length === 0) {
        aprobadasList.innerHTML = '<p style="color: var(--text-secondary);">No hay leyes aprobadas aún.</p>';
    } else {
        aprobadas.forEach(law => {
            aprobadasList.appendChild(createLawCard(law));
        });
    }
    
    if (rechazadas.length === 0) {
        rechazadasList.innerHTML = '<p style="color: var(--text-secondary);">No hay leyes rechazadas aún.</p>';
    } else {
        rechazadas.forEach(law => {
            rechazadasList.appendChild(createLawCard(law));
        });
    }
}

function createLawCard(law) {
    const card = document.createElement('div');
    card.className = 'law-card';
    card.onclick = () => openLawModal(law.id);
    
    const votesYes = law.votes_yes || 0;
    const votesNo = law.votes_no || 0;
    const totalVotes = votesYes + votesNo;
    const percentage = totalVotes > 0 ? Math.round((votesYes / totalVotes) * 100) : 0;
    
    card.innerHTML = `
        <div class="law-card-header">
            <div>
                <h3>${escapeHtml(law.title)}</h3>
                <div class="law-meta">
                    Propuesta por ${escapeHtml(law.author_name)} el ${formatDate(law.created_at)}
                </div>
            </div>
            <div class="law-votes">
                <div class="vote-count positive">
                    👍 ${votesYes}
                </div>
                <div class="vote-count negative">
                    👎 ${votesNo}
                </div>
            </div>
        </div>
        <div style="color: var(--text-secondary); margin-top: 0.5rem;">
            ${percentage}% de aprobación
        </div>
    `;
    
    return card;
}

async function openLawModal(lawId) {
    currentLawId = lawId;
    
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_law_details',
                law_id: lawId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayLawDetails(data.law, data.comments);
        }
    } catch (error) {
        console.error('Error loading law details:', error);
    }
}

function displayLawDetails(law, comments) {
    const modalBody = document.getElementById('modal-body');
    
    const votesYes = law.votes_yes || 0;
    const votesNo = law.votes_no || 0;
    const totalVotes = votesYes + votesNo;
    const percentage = totalVotes > 0 ? Math.round((votesYes / totalVotes) * 100) : 0;
    
    const userVoted = law.user_vote;
    const canVote = law.status === 'propuesta';
    
    modalBody.innerHTML = `
        <div class="law-detail">
            <h2>${escapeHtml(law.title)}</h2>
            <div class="law-detail-meta">
                Propuesta por ${escapeHtml(law.author_name)} el ${formatDate(law.created_at)}<br>
                Estado: <strong>${law.status.charAt(0).toUpperCase() + law.status.slice(1)}</strong>
            </div>
            
            <div class="law-description">
                ${escapeHtml(law.description).replace(/\n/g, '<br>')}
            </div>
            
            <div class="voting-section">
                <div style="width: 100%; text-align: center; margin-bottom: 1rem;">
                    <strong>${percentage}% de aprobación</strong><br>
                    <span style="color: var(--text-secondary);">
                        ${votesYes} a favor, ${votesNo} en contra
                    </span>
                </div>
                ${canVote ? `
                    <button class="vote-button vote-yes" onclick="voteLaw('yes')" ${userVoted === 'yes' ? 'disabled' : ''}>
                        👍 Votar a Favor ${userVoted === 'yes' ? '(Ya votaste)' : ''}
                    </button>
                    <button class="vote-button vote-no" onclick="voteLaw('no')" ${userVoted === 'no' ? 'disabled' : ''}>
                        👎 Votar en Contra ${userVoted === 'no' ? '(Ya votaste)' : ''}
                    </button>
                ` : `
                    <div style="text-align: center; width: 100%; color: var(--text-secondary);">
                        La votación ha finalizado
                    </div>
                `}
            </div>
            
            <div class="comments-section">
                <h3>Comentarios</h3>
                
                ${canVote ? `
                    <div class="comment-form">
                        <textarea id="comment-text" placeholder="Escribe tu comentario..." rows="3"></textarea>
                        <button class="btn btn-primary" onclick="submitComment()">Publicar Comentario</button>
                    </div>
                ` : ''}
                
                <div class="comments-list" id="comments-list">
                    ${comments.length === 0 ? '<p style="color: var(--text-secondary);">No hay comentarios aún.</p>' : ''}
                </div>
            </div>
        </div>
    `;
    
    if (comments.length > 0) {
        displayComments(comments);
    }
    
    document.getElementById('law-modal').classList.add('show');
}

function displayComments(comments) {
    const commentsList = document.getElementById('comments-list');
    
    // Sort comments by score (upvotes - downvotes)
    comments.sort((a, b) => {
        const scoreA = (a.upvotes || 0) - (a.downvotes || 0);
        const scoreB = (b.upvotes || 0) - (b.downvotes || 0);
        return scoreB - scoreA;
    });
    
    commentsList.innerHTML = '';
    
    comments.forEach(comment => {
        const commentEl = document.createElement('div');
        commentEl.className = 'comment';
        
        const score = (comment.upvotes || 0) - (comment.downvotes || 0);
        const userVote = comment.user_vote;
        
        commentEl.innerHTML = `
            <div class="comment-header">
                <span class="comment-author">${escapeHtml(comment.author_name)}</span>
                <span class="comment-date">${formatDate(comment.created_at)}</span>
            </div>
            <div class="comment-text">${escapeHtml(comment.text)}</div>
            <div class="comment-actions">
                <button class="comment-vote-btn ${userVote === 'up' ? 'upvoted' : ''}" 
                        onclick="voteComment('${comment.id}', 'up')">
                    👍 ${comment.upvotes || 0}
                </button>
                <button class="comment-vote-btn ${userVote === 'down' ? 'downvoted' : ''}" 
                        onclick="voteComment('${comment.id}', 'down')">
                    👎 ${comment.downvotes || 0}
                </button>
                <span style="margin-left: 0.5rem; font-weight: 500;">
                    Score: ${score > 0 ? '+' : ''}${score}
                </span>
            </div>
        `;
        
        commentsList.appendChild(commentEl);
    });
}

async function voteLaw(vote) {
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'vote_law',
                law_id: currentLawId,
                vote: vote
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload law details
            openLawModal(currentLawId);
            loadLaws();
        } else {
            alert(data.message || 'Error al votar');
        }
    } catch (error) {
        console.error('Error voting:', error);
        alert('Error de conexión');
    }
}

async function submitComment() {
    const commentText = document.getElementById('comment-text').value.trim();
    
    if (!commentText) {
        alert('Por favor escribe un comentario');
        return;
    }
    
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add_comment',
                law_id: currentLawId,
                text: commentText
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('comment-text').value = '';
            openLawModal(currentLawId);
        } else {
            alert(data.message || 'Error al publicar comentario');
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
        alert('Error de conexión');
    }
}

async function voteComment(commentId, vote) {
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'vote_comment',
                comment_id: commentId,
                vote: vote
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            openLawModal(currentLawId);
        }
    } catch (error) {
        console.error('Error voting comment:', error);
    }
}

async function handleNewProposal(event) {
    event.preventDefault();
    
    const title = document.getElementById('proposal-title').value;
    const description = document.getElementById('proposal-description').value;
    
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'create_law',
                title: title,
                description: description
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('proposal-message', 'Propuesta publicada exitosamente', 'success');
            document.querySelector('.proposal-form').reset();
            loadLaws();
            // Switch to propuestas section
            setTimeout(() => {
                document.querySelector('.nav-btn').click();
            }, 1500);
        } else {
            showMessage('proposal-message', data.message || 'Error al publicar propuesta', 'error');
        }
    } catch (error) {
        console.error('Error creating proposal:', error);
        showMessage('proposal-message', 'Error de conexión', 'error');
    }
    
    return false;
}

function closeModal() {
    document.getElementById('law-modal').classList.remove('show');
    currentLawId = null;
}

function showMessage(elementId, message, type) {
    const messageEl = document.getElementById(elementId);
    if (messageEl) {
        messageEl.textContent = message;
        messageEl.className = `message show ${type}`;
        setTimeout(() => {
            messageEl.classList.remove('show');
        }, 5000);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-AR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('law-modal');
    if (event.target === modal) {
        closeModal();
    }
}

