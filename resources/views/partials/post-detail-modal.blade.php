{{-- ========================================================================= --}}
{{-- MODAL DETAIL POSTINGAN (INSTAGRAM WEB STYLE)                              --}}
{{-- ========================================================================= --}}
<div id="post-detail-modal"
     class="fixed inset-0 z-[70] items-center justify-center p-3 sm:p-6 bg-slate-950/80 backdrop-blur-md animate-fade-in"
     style="display: none;"
     onclick="if (event.target === this) closePostDetailModal();">

    <!-- Modal Card (2-Kolom di Desktop) -->
    <div class="relative flex flex-col lg:flex-row w-full max-w-5xl max-h-[92vh] overflow-hidden rounded-[28px] bg-white shadow-2xl border border-slate-100 z-[75]">
        
        <!-- SISI KIRI: Foto Postingan -->
        <div class="relative flex-1 bg-black flex items-center justify-center min-h-[300px] lg:min-h-[580px] overflow-hidden">
            <!-- Loading Spinner for Image (Eliminates Flash of Previous Post) -->
            <div id="detail-image-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 text-white z-10 transition-opacity duration-150">
                <div class="animate-spin h-8 w-8 border-3 border-[#4154e8] border-t-transparent rounded-full mb-2"></div>
                <span class="text-xs text-slate-400 font-semibold">Memuat postingan...</span>
            </div>

            <img id="detail-post-image" src="" alt="Post image" class="max-h-[50vh] lg:max-h-[85vh] w-full object-contain opacity-0 transition-opacity duration-200">
            <span id="detail-post-badge" class="absolute top-4 left-4 rounded-full bg-black/60 px-3 py-1 text-xs font-black text-white backdrop-blur-md z-20">
                POST
            </span>
        </div>

        <!-- SISI KANAN: Detail & Komentar (Lebar 380px - 440px) -->
        <div class="flex flex-col w-full lg:w-[420px] bg-white border-t lg:border-t-0 lg:border-l border-slate-100 min-h-[340px] lg:min-h-[580px]">
            
            <!-- 1. Header Author -->
            <div class="flex items-center justify-between p-4 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <a id="detail-author-link" href="#" class="shrink-0">
                        <div id="detail-author-avatar-wrap" class="relative">
                            <img id="detail-author-avatar" src="" alt="" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100">
                            <div id="detail-author-fallback" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#4154e8] text-white text-xs font-black ring-2 ring-slate-100">
                                US
                            </div>
                        </div>
                    </a>
                    <div class="min-w-0">
                        <a id="detail-author-name" href="#" class="block text-xs font-extrabold text-slate-900 hover:text-[#4154e8] truncate leading-tight">
                            User
                        </a>
                        <span id="detail-author-username" class="text-[11px] text-slate-400 font-semibold truncate block">
                            @username
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="detail-follow-btn" onclick="toggleFollowAuthorFromDetail()"
                            class="hidden rounded-full bg-[#4154e8] hover:bg-[#3243d4] px-3.5 py-1 text-[11px] font-extrabold text-white transition shadow-sm">
                        Follow
                    </button>
                    <button type="button" id="detail-delete-btn" onclick="deleteCurrentPost()"
                            class="hidden p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus postingan saya">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button type="button" onclick="event.stopPropagation(); closePostDetailModal();" class="p-2 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Tutup">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 2. Scrollable Body: Caption + List Komentar -->
            <div id="detail-scroll-area" class="flex-1 overflow-y-auto p-4 space-y-4">
                
                <!-- Caption Author Post -->
                <div class="flex items-start gap-3 pb-3 border-b border-slate-100">
                    <div class="shrink-0" id="detail-caption-avatar-slot">
                        <!-- Populated by JS -->
                    </div>
                    <div class="min-w-0 text-xs text-slate-800 leading-relaxed">
                        <p>
                            <span id="detail-caption-username" class="font-black text-slate-900 mr-1"></span>
                            <span id="detail-caption-text" class="whitespace-pre-line text-slate-700"></span>
                        </p>
                        <p id="detail-post-time" class="text-[10px] text-slate-400 font-semibold mt-1.5"></p>
                    </div>
                </div>

                <!-- Komentar Container -->
                <div id="detail-comments-list" class="space-y-3.5">
                    <!-- Populated by JS -->
                    <div class="py-8 text-center text-xs text-slate-400">
                        <div class="animate-spin h-5 w-5 border-2 border-[#4154e8] border-t-transparent rounded-full mx-auto mb-2"></div>
                        Memuat komentar...
                    </div>
                </div>
            </div>

            <!-- 3. Action Bar (Like, Comment, Save, DM) -->
            <div class="p-4 border-t border-slate-100 shrink-0 bg-white space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-2 py-1">
                            <button type="button" onclick="toggleDetailReaction('like')" id="detail-like-btn"
                                    class="inline-flex items-center gap-1 text-slate-600 transition hover:text-emerald-700" aria-label="Suka">
                                <span id="detail-like-icon">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 10v12m0-12 4-8a3 3 0 0 1 3 3v3h5a2 2 0 0 1 2 2l-1.5 9a2 2 0 0 1-2 2H7m0-12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/></svg>
                                </span>
                                <span id="detail-likes-count" class="text-xs font-bold">0</span>
                            </button>
                            <span class="h-5 w-px bg-slate-200"></span>
                            <button type="button" onclick="toggleDetailReaction('dislike')" id="detail-dislike-btn"
                                    class="inline-flex items-center gap-1 text-slate-600 transition hover:text-rose-700" aria-label="Tidak suka">
                                <span id="detail-dislike-icon">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14V2m0 12-4 8a3 3 0 0 1-3-3v-3H5a2 2 0 0 1-2-2l1.5-9a2 2 0 0 1 2-2H17m0 12h3a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/></svg>
                                </span>
                                <span id="detail-dislikes-count" class="text-xs font-bold">0</span>
                            </button>
                        </div>

                        <!-- Focus Comment Input -->
                        <button type="button" onclick="document.getElementById('detail-comment-input').focus()"
                                class="text-slate-600 hover:text-[#4154e8] transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>

                        <!-- Kirim Pesan DM -->
                        <a id="detail-dm-link" href="#" class="text-slate-600 hover:text-[#4154e8] transition" title="Kirim DM ke author">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </a>
                    </div>

                    <!-- Bookmark / Save Button -->
                    <button type="button" onclick="toggleDetailSave()" id="detail-save-btn"
                            class="text-slate-400 hover:text-[#4154e8] transition" title="Simpan ke koleksi privat">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </button>
                </div>

                <!-- Likes count & Timestamp -->
                <div>
                    <span id="detail-created-date" class="block text-[10px] text-slate-400 mt-0.5 font-medium"></span>
                </div>
            </div>

            <!-- 4. Input Tambah Komentar -->
            <div class="p-3 border-t border-slate-100 shrink-0 bg-slate-50/50">
                <!-- Reply Indicator -->
                <div id="detail-reply-indicator" class="hidden items-center justify-between px-2 py-1 mb-1.5 rounded-lg bg-blue-50 text-[11px] text-[#4154e8] font-bold">
                    <span>Membalas <span id="detail-reply-username">@user</span></span>
                    <button type="button" onclick="cancelDetailReply()" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <form onsubmit="submitDetailComment(event)" class="flex items-center gap-2">
                    <input type="text" id="detail-comment-input" placeholder="Tambahkan komentar..."
                           class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4154e8] transition">
                    <button type="submit"
                            class="rounded-xl bg-[#4154e8] hover:bg-[#3243d4] px-4 py-2 text-xs font-extrabold text-white transition active:scale-95 shadow-sm">
                        Kirim
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS KOMENTAR (Kekinian, Menarik & Tampilkan Preview Komentar) -->
<div id="delete-comment-modal"
     style="display: none;"
     class="fixed inset-0 z-[95] items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm animate-fade-in"
     onclick="if (event.target === this) closeDeleteCommentModal();">

    <div class="relative w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl border border-slate-100 text-center transform transition-all duration-200">
        
        <!-- Trash Icon with Soft Rose Glow -->
        <div class="mx-auto mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 ring-6 ring-rose-50/60 shadow-inner">
            <svg class="h-6 w-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>

        <!-- Title & Subtitle -->
        <h3 class="text-base font-black text-slate-900 tracking-tight">Hapus Komentar?</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            Apakah kamu yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.
        </p>

        <!-- Preview Komentar yang Ingin Dihapus (Sesuai Permintaan User) -->
        <div class="my-4 rounded-2xl bg-slate-50 border border-slate-200/80 p-3.5 flex items-start gap-3 text-left">
            <div id="del-comment-avatar-slot" class="shrink-0 pt-0.5">
                <!-- Avatar injected via JS -->
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-black text-slate-900 leading-tight truncate" id="del-comment-username">@username</p>
                <p class="text-xs text-slate-700 mt-1.5 leading-relaxed break-words line-clamp-3 bg-white p-2.5 rounded-xl border border-slate-100 italic" id="del-comment-body">
                    "Isi komentar yang ingin dihapus..."
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 pt-1">
            <button type="button" onclick="closeDeleteCommentModal()"
                    class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 py-2.5 text-xs font-extrabold text-slate-700 transition active:scale-95 cursor-pointer">
                Batal
            </button>
            <button type="button" id="del-comment-confirm-btn" onclick="executeDeleteComment()"
                    class="flex-1 rounded-xl bg-rose-600 hover:bg-rose-700 py-2.5 text-xs font-extrabold text-white transition active:scale-95 shadow-md shadow-rose-500/25 cursor-pointer">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
let currentDetailPostId = null;
let currentDetailPostData = null;
let detailReplyParentId = null;
let detailModalRequestSeq = 0;

function resetDetailModalState() {
    // Show image loader and hide previous image to prevent flash of old content
    const loader = document.getElementById('detail-image-loader');
    if (loader) {
        loader.style.display = 'flex';
        loader.classList.remove('opacity-0');
    }
    const img = document.getElementById('detail-post-image');
    if (img) {
        img.src = '';
        img.classList.add('opacity-0');
    }

    // Reset textual fields
    const authorName = document.getElementById('detail-author-name');
    if (authorName) authorName.innerText = 'Memuat...';
    const authorUser = document.getElementById('detail-author-username');
    if (authorUser) authorUser.innerText = '@...';
    const captionUsername = document.getElementById('detail-caption-username');
    if (captionUsername) captionUsername.innerText = '';
    const captionText = document.getElementById('detail-caption-text');
    if (captionText) captionText.innerText = 'Memuat rincian postingan...';
    const postTime = document.getElementById('detail-post-time');
    if (postTime) postTime.innerText = '';

    // Reset comments list with spinner
    const commentsList = document.getElementById('detail-comments-list');
    if (commentsList) {
        commentsList.innerHTML = `
            <div class="py-8 text-center text-xs text-slate-400">
                <div class="animate-spin h-5 w-5 border-2 border-[#4154e8] border-t-transparent rounded-full mx-auto mb-2"></div>
                Memuat komentar...
            </div>`;
    }

    const likesCount = document.getElementById('detail-likes-count');
    if (likesCount) likesCount.innerText = '0 suka';
}

function openPostDetailModal(postId) {
    currentDetailPostId = postId;
    detailReplyParentId = null;
    const thisRequestId = ++detailModalRequestSeq;

    const replyInd = document.getElementById('detail-reply-indicator');
    if (replyInd) replyInd.classList.add('hidden');
    const commentInp = document.getElementById('detail-comment-input');
    if (commentInp) commentInp.value = '';

    // Immediately wipe old post content so user NEVER sees previous post
    resetDetailModalState();

    const modal = document.getElementById('post-detail-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // Fetch post details & comments
    fetch('/posts/' + postId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Discard if user already clicked another post
        if (thisRequestId !== detailModalRequestSeq) return;

        if (!data.success) {
            closePostDetailModal();
            return;
        }

        currentDetailPostData = data.post;
        renderPostDetailModal(data.post, data.comments);
    })
    .catch(() => {
        if (thisRequestId === detailModalRequestSeq) {
            closePostDetailModal();
        }
    });
}

function closePostDetailModal() {
    const modal = document.getElementById('post-detail-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    document.body.style.overflow = '';
    currentDetailPostId = null;
    currentDetailPostData = null;
    resetDetailModalState();
}

// Close on Escape key press
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('post-detail-modal');
        if (modal && modal.style.display !== 'none' && !modal.classList.contains('hidden')) {
            closePostDetailModal();
        }
    }
});

function renderPostDetailModal(post, comments) {
    // Foto & Badge with smooth fade-in and loader dismiss
    const img = document.getElementById('detail-post-image');
    const loader = document.getElementById('detail-image-loader');
    
    img.onload = function() {
        if (loader) {
            loader.classList.add('opacity-0');
            setTimeout(() => { loader.style.display = 'none'; }, 150);
        }
        img.classList.remove('opacity-0');
    };
    img.src = post.image;
    if (img.complete) {
        if (loader) {
            loader.classList.add('opacity-0');
            setTimeout(() => { loader.style.display = 'none'; }, 150);
        }
        img.classList.remove('opacity-0');
    }

    document.getElementById('detail-post-badge').innerText = post.type || 'POST';

    // Author
    document.getElementById('detail-author-name').innerText = post.author_name;
    document.getElementById('detail-author-username').innerText = '@' + post.author_username;
    document.getElementById('detail-author-link').href = '/profile/' + post.author_username;
    document.getElementById('detail-author-name').href = '/profile/' + post.author_username;
    document.getElementById('detail-dm-link').href = '/chat?user=' + post.author_username;

    const avatarImg = document.getElementById('detail-author-avatar');
    const avatarFallback = document.getElementById('detail-author-fallback');
    if (post.author_avatar) {
        avatarImg.src = post.author_avatar;
        avatarImg.classList.remove('hidden');
        avatarFallback.classList.add('hidden');
    } else {
        avatarImg.classList.add('hidden');
        avatarFallback.innerText = post.author_initials;
        avatarFallback.classList.remove('hidden');
    }

    // Follow / Delete button
    const followBtn = document.getElementById('detail-follow-btn');
    const deleteBtn = document.getElementById('detail-delete-btn');
    if (post.is_own_post) {
        followBtn.classList.add('hidden');
        deleteBtn.classList.remove('hidden');
    } else {
        deleteBtn.classList.add('hidden');
        followBtn.classList.remove('hidden');
        if (post.is_following_author) {
            followBtn.className = 'rounded-full bg-slate-100 hover:bg-slate-200 px-3.5 py-1 text-[11px] font-extrabold text-slate-700 transition';
            followBtn.innerText = 'Following';
        } else {
            followBtn.className = 'rounded-full bg-[#4154e8] hover:bg-[#3243d4] px-3.5 py-1 text-[11px] font-extrabold text-white transition shadow-sm';
            followBtn.innerText = 'Follow';
        }
    }

    // Caption
    document.getElementById('detail-caption-username').innerText = post.author_username;
    document.getElementById('detail-caption-text').innerText = post.caption || 'Tanpa keterangan';
    document.getElementById('detail-post-time').innerText = post.created_at;

    const captionAvatarSlot = document.getElementById('detail-caption-avatar-slot');
    captionAvatarSlot.innerHTML = post.author_avatar
        ? `<img src="${post.author_avatar}" class="h-8 w-8 rounded-full object-cover">`
        : `<div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4154e8] text-white text-[10px] font-black">${post.author_initials}</div>`;

    // Like state
    updateDetailLikeState(post.reaction, post.likes_count, post.dislikes_count);

    // Save state
    updateDetailSaveState(post.is_saved);

    // Date
    document.getElementById('detail-created-date').innerText = post.created_date;

    // Comments List
    renderDetailComments(comments);
}

function updateDetailLikeState(reaction, likesCount, dislikesCount) {
    const likeBtn = document.getElementById('detail-like-btn');
    const likeIcon = document.getElementById('detail-like-icon');
    const dislikeBtn = document.getElementById('detail-dislike-btn');
    const dislikeIcon = document.getElementById('detail-dislike-icon');

    document.getElementById('detail-likes-count').textContent = likesCount;
    document.getElementById('detail-dislikes-count').textContent = dislikesCount;

    likeBtn.className = `relative inline-flex items-center gap-1 transition ${reaction === 'like' ? 'text-[#4154e8]' : 'text-slate-600 hover:text-[#4154e8]'}`;
    dislikeBtn.className = `relative inline-flex items-center gap-1 transition ${reaction === 'dislike' ? 'text-[#4154e8]' : 'text-slate-600 hover:text-[#4154e8]'}`;
    likeBtn.setAttribute('aria-pressed', reaction === 'like' ? 'true' : 'false');
    dislikeBtn.setAttribute('aria-pressed', reaction === 'dislike' ? 'true' : 'false');
    likeIcon.innerHTML = `<svg class="h-5 w-5 ${reaction === 'like' ? 'fill-blue-100' : ''}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 10v12m0-12 4-8a3 3 0 0 1 3 3v3h5a2 2 0 0 1 2 2l-1.5 9a2 2 0 0 1-2 2H7m0-12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/></svg>`;
    dislikeIcon.innerHTML = `<svg class="h-5 w-5 ${reaction === 'dislike' ? 'fill-blue-100' : ''}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14V2m0 12-4 8a3 3 0 0 1-3-3v-3H5a2 2 0 0 1-2-2l1.5-9A2 2 0 0 1 4.5 3H17m0 11h3a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/></svg>`;
}

function updateDetailSaveState(isSaved) {
    const saveBtn = document.getElementById('detail-save-btn');
    if (!saveBtn) return;
    saveBtn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
    saveBtn.title = isSaved ? 'Hapus dari simpanan' : 'Simpan ke koleksi privat';
    if (isSaved) {
        saveBtn.className = 'text-[#4154e8] transition scale-110';
        saveBtn.innerHTML = `<svg class="h-5 w-5 fill-[#4154e8]" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>`;
    } else {
        saveBtn.className = 'text-slate-400 hover:text-[#4154e8] transition';
        saveBtn.innerHTML = `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>`;
    }
}

function toggleDetailReaction(reaction) {
    if (!currentDetailPostId || !currentDetailPostData) return;

    const postId = currentDetailPostId;
    const previous = {
        reaction: currentDetailPostData.reaction || null,
        likes: Number(currentDetailPostData.likes_count),
        dislikes: Number(currentDetailPostData.dislikes_count),
    };
    const nextReaction = previous.reaction === reaction ? null : reaction;
    const nextLikes = previous.likes + (nextReaction === 'like' ? 1 : 0) - (previous.reaction === 'like' ? 1 : 0);
    const nextDislikes = previous.dislikes + (nextReaction === 'dislike' ? 1 : 0) - (previous.reaction === 'dislike' ? 1 : 0);
    const sequence = (window.detailReactionRequestSequence || 0) + 1;
    window.detailReactionRequestSequence = sequence;
    currentDetailPostData.reaction = nextReaction;
    currentDetailPostData.likes_count = nextLikes;
    currentDetailPostData.dislikes_count = nextDislikes;
    updateDetailLikeState(nextReaction, nextLikes, nextDislikes);
    window.animateReactionParticles(document.getElementById(reaction === 'like' ? 'detail-like-btn' : 'detail-dislike-btn'), reaction);
    updatePostReactionFeed(postId, nextReaction, nextLikes, nextDislikes);

    fetch('/posts/' + currentDetailPostId + '/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reaction })
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.error || 'Reaksi gagal disimpan.');
        return data;
    })
    .then((data) => {
        if (window.detailReactionRequestSequence !== sequence || currentDetailPostId !== postId) return;
        currentDetailPostData.reaction = data.reaction;
        currentDetailPostData.likes_count = data.likes_count;
        currentDetailPostData.dislikes_count = data.dislikes_count;
        updateDetailLikeState(data.reaction, data.likes_count, data.dislikes_count);
        updatePostReactionFeed(postId, data.reaction, data.likes_count, data.dislikes_count);
    })
    .catch((error) => {
        if (window.detailReactionRequestSequence !== sequence || currentDetailPostId !== postId) return;
        Object.assign(currentDetailPostData, {
            reaction: previous.reaction,
            likes_count: previous.likes,
            dislikes_count: previous.dislikes,
        });
        updateDetailLikeState(previous.reaction, previous.likes, previous.dislikes);
        updatePostReactionFeed(postId, previous.reaction, previous.likes, previous.dislikes);
        window.alert(error.message || 'Reaksi gagal disimpan. Coba lagi.');
    });
}

function updatePostReactionFeed(postId, reaction, likes, dislikes) {
    const likesCount = document.getElementById('like-count-' + postId);
    const dislikesCount = document.getElementById('dislike-count-' + postId);
    if (likesCount) likesCount.textContent = likes;
    if (dislikesCount) dislikesCount.textContent = dislikes;

    document.querySelectorAll(`.post-reaction-button[data-post-id="${postId}"]`).forEach((button) => {
        const active = button.dataset.reaction === reaction;
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.classList.toggle('bg-emerald-50', active && reaction === 'like');
        button.classList.toggle('text-emerald-700', active && reaction === 'like');
        button.classList.toggle('bg-rose-50', active && reaction === 'dislike');
        button.classList.toggle('text-rose-700', active && reaction === 'dislike');
        button.classList.toggle('text-slate-600', !active);
    });
}

function toggleDetailSave() {
    if (!currentDetailPostId || !currentDetailPostData) return;

    const postId = currentDetailPostId;
    const oldSaved = Boolean(currentDetailPostData.is_saved);
    const nextSaved = !oldSaved;
    const sequence = (window.detailSaveRequestSequence || 0) + 1;
    window.detailSaveRequestSequence = sequence;
    currentDetailPostData.is_saved = nextSaved;
    updateDetailSaveState(nextSaved);
    window.setPostSavedState(postId, nextSaved);

    fetch('/posts/' + postId + '/toggle-save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || data.error || 'Simpanan gagal diperbarui.');
        return data;
    })
    .then((data) => {
        if (window.detailSaveRequestSequence !== sequence || currentDetailPostId !== postId) return;
        currentDetailPostData.is_saved = data.saved;
        updateDetailSaveState(data.saved);
        window.setPostSavedState(postId, data.saved);
    })
    .catch((error) => {
        if (window.detailSaveRequestSequence !== sequence || currentDetailPostId !== postId) return;
        currentDetailPostData.is_saved = oldSaved;
        updateDetailSaveState(oldSaved);
        window.setPostSavedState(postId, oldSaved);
        window.alert(error.message || 'Simpanan gagal diperbarui. Coba lagi.');
    });
}

window.currentDetailCommentsMap = window.currentDetailCommentsMap || {};
let currentDetailCommentsMap = window.currentDetailCommentsMap;
let pendingDeleteCommentId = null;

function renderDetailComments(comments) {
    window.currentDetailCommentsMap = {};
    currentDetailCommentsMap = window.currentDetailCommentsMap;
    const list = document.getElementById('detail-comments-list');
    if (!comments || comments.length === 0) {
        list.innerHTML = `<div class="py-8 text-center text-xs text-slate-400">Belum ada komentar. Jadilah yang pertama berkomentar!</div>`;
        return;
    }

    // Index all comments and replies for quick modal preview lookup
    comments.forEach(c => {
        currentDetailCommentsMap[c.id] = c;
        if (c.replies && c.replies.length > 0) {
            c.replies.forEach(r => {
                currentDetailCommentsMap[r.id] = r;
            });
        }
    });

    list.innerHTML = comments.map(c => `
        <div class="space-y-2" id="detail-comment-${c.id}">
            <div class="flex items-start justify-between gap-2.5 group">
                <div class="flex items-start gap-2.5 min-w-0">
                    <a href="/profile/${c.user_username}" class="shrink-0">
                        ${c.user_avatar
                            ? `<img src="${c.user_avatar}" class="h-7 w-7 rounded-full object-cover">`
                            : `<div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#4154e8] text-white text-[9px] font-black">${c.user_initials}</div>`
                        }
                    </a>
                    <div class="min-w-0 text-xs text-slate-800 leading-snug">
                        <p>
                            <a href="/profile/${c.user_username}" class="font-extrabold text-slate-900 mr-1 hover:underline">${c.user_username}</a>
                            <span>${c.body}</span>
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-[10px] text-slate-400 font-semibold">
                            <span>${c.created_at}</span>
                            <button type="button" onclick="startDetailReply(${c.id}, '${c.user_username}')" class="text-slate-600 font-bold hover:text-[#4154e8] cursor-pointer">Balas</button>
                            ${c.can_delete ? `<button type="button" onclick="promptDeleteComment(${c.id})" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">Hapus</button>` : ''}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies list -->
            ${c.replies && c.replies.length > 0 ? `
                <div class="ml-9 pl-3 border-l-2 border-slate-100 space-y-2">
                    ${c.replies.map(r => `
                        <div class="flex items-start justify-between gap-2" id="detail-comment-${r.id}">
                            <div class="flex items-start gap-2 min-w-0">
                                <a href="/profile/${r.user_username}" class="shrink-0">
                                    ${r.user_avatar
                                        ? `<img src="${r.user_avatar}" class="h-6 w-6 rounded-full object-cover">`
                                        : `<div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#4154e8] text-white text-[8px] font-black">${r.user_initials}</div>`
                                    }
                                </a>
                                <div class="min-w-0 text-xs text-slate-800 leading-snug">
                                    <p>
                                        <a href="/profile/${r.user_username}" class="font-extrabold text-slate-900 mr-1 hover:underline">${r.user_username}</a>
                                        <span>${r.body}</span>
                                    </p>
                                    <div class="flex items-center gap-3 mt-1 text-[10px] text-slate-400 font-semibold">
                                        <span>${r.created_at}</span>
                                        ${r.can_delete ? `<button type="button" onclick="promptDeleteComment(${r.id})" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">Hapus</button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
        </div>
    `).join('');
}

function startDetailReply(commentId, username) {
    detailReplyParentId = commentId;
    document.getElementById('detail-reply-username').innerText = '@' + username;
    document.getElementById('detail-reply-indicator').classList.remove('hidden');
    document.getElementById('detail-reply-indicator').classList.add('flex');
    const input = document.getElementById('detail-comment-input');
    input.focus();
}

function cancelDetailReply() {
    detailReplyParentId = null;
    document.getElementById('detail-reply-indicator').classList.add('hidden');
}

function submitDetailComment(e) {
    e.preventDefault();
    const input = document.getElementById('detail-comment-input');
    const body = input.value.trim();
    if (!body || !currentDetailPostId) return;

    fetch('/posts/' + currentDetailPostId + '/comments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ body: body, parent_id: detailReplyParentId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            cancelDetailReply();
            // Reload comments
            fetch('/posts/' + currentDetailPostId + '/comments')
                .then(r => r.json())
                .then(res => {
                    if (res.success) renderDetailComments(res.comments);
                });
        }
    });
}

// ─── CUSTOM CONFIRMATION MODAL UNTUK HAPUS KOMENTAR ─────────────────────────────
function promptDeleteComment(commentId) {
    const c = currentDetailCommentsMap[commentId];
    if (!c) return;

    pendingDeleteCommentId = commentId;

    // Set preview details di dalam modal konfirmasi
    const userEl = document.getElementById('del-comment-username');
    if (userEl) userEl.innerText = '@' + c.user_username;

    const bodyEl = document.getElementById('del-comment-body');
    if (bodyEl) bodyEl.innerText = `"${c.body}"`;

    const avatarSlot = document.getElementById('del-comment-avatar-slot');
    if (avatarSlot) {
        avatarSlot.innerHTML = c.user_avatar
            ? `<img src="${c.user_avatar}" class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-100">`
            : `<div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4154e8] text-white text-[10px] font-black">${c.user_initials}</div>`;
    }

    const modal = document.getElementById('delete-comment-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }
}

function closeDeleteCommentModal() {
    const modal = document.getElementById('delete-comment-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    pendingDeleteCommentId = null;
}

function executeDeleteComment() {
    if (!pendingDeleteCommentId) return;

    const btn = document.getElementById('del-comment-confirm-btn');
    if (btn) {
        btn.innerText = 'Menghapus...';
        btn.disabled = true;
    }

    const idToDelete = pendingDeleteCommentId;

    fetch('/comments/' + idToDelete, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) {
            btn.innerText = 'Hapus';
            btn.disabled = false;
        }

        closeDeleteCommentModal();

        if (data.success) {
            const el = document.getElementById('detail-comment-' + idToDelete);
            if (el) el.remove();

            // Support juga quick comments modal pada community feed jika sedang dibuka
            const comNode = document.getElementById('comment-node-' + idToDelete);
            if (comNode) {
                comNode.style.transition = 'opacity 0.2s, transform 0.2s';
                comNode.style.opacity = '0';
                comNode.style.transform = 'scale(0.95)';
                setTimeout(() => comNode.remove(), 200);
            }
            if (typeof activeCommentsPostId !== 'undefined' && activeCommentsPostId) {
                const cardCount = document.getElementById('comment-count-' + activeCommentsPostId);
                if (cardCount && data.comments_count !== undefined) cardCount.textContent = data.comments_count;
                const pill = document.getElementById('modal-comments-count-pill');
                if (pill && data.comments_count !== undefined) pill.textContent = data.comments_count;
            }

            // Modal notif SweetAlert dengan animasi ceklis modern khusus saat hapus komen
            if (window.showSuccessToast) {
                showSuccessToast('Komentar Berhasil Dihapus', 'Komentar kamu telah berhasil dihapus dari postingan.');
            }
        }
    })
    .catch(() => {
        if (btn) {
            btn.innerText = 'Hapus';
            btn.disabled = false;
        }
        closeDeleteCommentModal();
        if (window.showErrorToast) {
            showErrorToast('Gagal Menghapus', 'Terjadi kesalahan sistem.');
        }
    });
}
window.promptDeleteComment = promptDeleteComment;
window.closeDeleteCommentModal = closeDeleteCommentModal;
window.executeDeleteComment = executeDeleteComment;

function deleteCurrentPost() {
    if (!currentDetailPostId) return;
    if (!confirm('Apakah kamu yakin ingin menghapus postingan ini secara permanen?')) return;

    fetch('/posts/' + currentDetailPostId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closePostDetailModal();
            if (window.showSuccessToast) {
                showSuccessToast('Postingan Dihapus!', 'Postingan berhasil dihapus dari sistem.');
            }
            setTimeout(() => { location.reload(); }, 800);
        }
    });
}

function toggleFollowAuthorFromDetail() {
    if (!currentDetailPostData || !currentDetailPostData.author_id) return;

    const btn = document.getElementById('detail-follow-btn');
    fetch('/users/' + currentDetailPostData.author_id + '/toggle-follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.following) {
                btn.className = 'rounded-full bg-slate-100 hover:bg-slate-200 px-3.5 py-1 text-[11px] font-extrabold text-slate-700 transition';
                btn.innerText = 'Following';
                if (window.showSuccessToast) {
                    showSuccessToast('Mengikuti Warga!', 'Kamu sekarang mengikuti @' + currentDetailPostData.author_username);
                }
            } else {
                btn.className = 'rounded-full bg-[#4154e8] hover:bg-[#3243d4] px-3.5 py-1 text-[11px] font-extrabold text-white transition shadow-sm';
                btn.innerText = 'Follow';
            }
        }
    });
}
</script>
