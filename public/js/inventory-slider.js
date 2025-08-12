// Inventory Image Slider functionality
class InventorySlider {
    constructor(containerId, totalSlides) {
        this.containerId = containerId;
        this.totalSlides = totalSlides;
        this.currentSlide = 0;
        this.autoSlideInterval = null;
        
        this.init();
    }
    
    init() {
        if (this.totalSlides > 1) {
            this.startAutoSlide();
        }
    }
    
    changeSlide(direction) {
        this.currentSlide = (this.currentSlide + direction + this.totalSlides) % this.totalSlides;
        this.updateSlider();
    }
    
    goToSlide(index) {
        this.currentSlide = index;
        this.updateSlider();
    }
    
    updateSlider() {
        const slider = document.getElementById('slider-images');
        const dots = document.querySelectorAll('.slider-dot');
        
        if (slider) {
            slider.style.transform = `translateX(-${this.currentSlide * 100}%)`;
        }
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }
    
    startAutoSlide() {
        this.autoSlideInterval = setInterval(() => {
            this.changeSlide(1);
        }, 5000); // Change slide every 5 seconds
    }
    
    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
        }
    }
}

// Global functions for onclick handlers
function changeSlide(direction) {
    if (window.inventorySlider) {
        window.inventorySlider.changeSlide(direction);
    }
}

function goToSlide(index) {
    if (window.inventorySlider) {
        window.inventorySlider.goToSlide(index);
    }
} 