// Function to create a new biodata card
function createBiodataCard(data) {
    const card = document.createElement('div');
    card.className = 'col-md-3';
    card.setAttribute('data-image-id', data.image_id);
    card.innerHTML = `
        <div class="biodata-card">
            <div class="card-menu">
                <button class="card-menu-btn" onclick="toggleMenu(this)">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="card-menu-content">
                    <button class="delete-btn" onclick="deleteCard(this, ${data.image_id})">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
            <img src="${data.image_path}" class="img-fluid" alt="Biodata Image">
            <div class="serial-number">S.No ${data.serial_number}</div>
            <div class="biodata-info">
                <h3>নাম: ${data.name}</h3>
                <p>বয়স: ${data.age}</p>
                <p class="location">ঠিকানা: ${data.address}</p>
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSfc7jGun221U3sJ6xN6jWuS-sm_-wUQFD4ZY3iPRz43FAq9QA/viewform" class="fill-form-btn">Log in</a>
            </div>
        </div>
    `;
    return card;
}

// Function to add a card to the appropriate galleries
function addCardToGalleries(data) {
    // Add to type-specific gallery
    const galleryId = data.type === 'men' ? 'menGallery' : 'womenGallery';
    const gallery = document.getElementById(galleryId);
    if (gallery) {
        const card = createBiodataCard(data);
        gallery.insertBefore(card, gallery.firstChild);
    }

    // Add to all gallery
    const allGallery = document.getElementById('allGallery');
    if (allGallery) {
        const allCard = createBiodataCard(data);
        allGallery.insertBefore(allCard, allGallery.firstChild);
    }
}

// Function to remove a card from all galleries
function removeCardFromGalleries(imageId) {
    ['allGallery', 'menGallery', 'womenGallery'].forEach(galleryId => {
        const gallery = document.getElementById(galleryId);
        if (gallery) {
            const card = gallery.querySelector(`[data-image-id="${imageId}"]`);
            if (card) {
                card.remove();
            }
        }
    });
}

// Function to handle card deletion
function deleteCard(button, imageId) {
    if (confirm('Are you sure you want to delete this card?')) {
        fetch('delete_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'image_id=' + imageId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeCardFromGalleries(imageId);
            } else {
                alert('Error deleting card: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting card');
        });
    }
}

// Function to toggle card menu
function toggleMenu(button) {
    const menu = button.nextElementSibling;
    const allMenus = document.querySelectorAll('.card-menu-content.show');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('show');
        }
    });
    
    // Toggle current menu
    menu.classList.toggle('show');
}

// Close all menus when clicking outside
document.addEventListener('click', function(event) {
    const menus = document.querySelectorAll('.card-menu-content.show');
    menus.forEach(menu => {
        if (!menu.contains(event.target) && !event.target.classList.contains('card-menu-btn')) {
            menu.classList.remove('show');
        }
    });
}); 