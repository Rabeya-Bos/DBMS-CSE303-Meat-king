const PRODUCTS = {
    items: [
        {
            id: 1,
            name: "Beef",
            price: 750,
            image: "../image/beef.jpeg",
            description: "Beef is a rich source of high-quality protein and various vitamins and minerals. As such, it can be an excellent component of a healthy diet.",
            category: "Meat",
            rating: 4.5,
            reviews: 128
        },
        {
            id: 2,
            name: "Chicken",
            price: 260,
            image: "../image/chicken.webp",
            description: "Chicken meat is a versatile, lean protein source, rich in nutrients, commonly used in cuisines worldwide for its flavor and texture.",
            category: "Meat",
            rating: 4.2,
            reviews: 95
        },
        // {
        //     id: 3,
        //     name: "Potatoes",
        //     price: 40,
        //     image: "../image/potato.jpg",
        //     description: "Potatoes are starchy root vegetables, rich in carbohydrates, versatile in cooking, and a staple food in many cultures worldwide.",
        //     category: "Vegetables",
        //     rating: 4.0,
        //     reviews: 67
        // },
        // {
        //     id: 4,
        //     name: "Catla",
        //     price: 400,
        //     image: "../image/katla.webp",
        //     description: "Catla also known as the major South Asian carp, is an economically important South Asian freshwater fish in the carp family Cyprinidae.",
        //     category: "Fish",
        //     rating: 4.7,
        //     reviews: 201
        // },
        // {
        //     id: 5,
        //     name: "Tomato",
        //     price: 50,
        //     image: "../image/Tomato_je.jpg",
        //     description: "Tomato is a juicy, red fruit commonly used in cooking, rich in vitamins, antioxidants, and widely cultivated worldwide.",
        //     category: "Vegetables",
        //     rating: 4.3,
        //     reviews: 112
        // },
        {
            id: 6,
            name: "Mutton",
            price: 1100,
            image: "../image/mutton.jpeg",
            description: "Mutton is the meat of a mature sheep, known for its rich flavor, tenderness, and use in various traditional dishes.",
            category: "Meat",
            rating: 4.1,
            reviews: 75
        },
        // {
        //     id: 7,
        //     name: "Chitol",
        //     price: 700,
        //     image: "../image/chitol.jpg",
        //     description: "Chitol fish, also known as clown knifefish, is a freshwater species prized for its tender, boneless flesh and mild flavor.",
        //     category: "Fish",
        //     rating: 4.8,
        //     reviews: 156
        // },
        {
            id: 8,
            name: "Deshi Duck",
            price: 560,
            image: "../image/duck.webp",
            description: "Deshi duck meat is rich, flavorful, and lean, known for its tenderness, distinctive taste, and high nutritional value.",
            category: "Meat",
            rating: 4.4,
            reviews: 89
        }
    ],
    
    getAll: function() {
        return this.items;
    },
    
    getProductById: function(id) {
        return this.items.find(product => product.id === parseInt(id));
    },
    
    getProductsByCategory: function(category) {
        return this.items.filter(product => product.category === category);
    },
    
    getFeaturedProducts: function(limit = 4) {
        return this.items.sort(() => 0.5 - Math.random()).slice(0, limit);
    },
    
    searchProducts: function(query) {
        query = query.toLowerCase();
        return this.items.filter(product => 
            product.name.toLowerCase().includes(query) || 
            product.description.toLowerCase().includes(query) ||
            product.category.toLowerCase().includes(query)
        );
    },
    
    renderProductCard: function(product) {
        const fullStars = Math.floor(product.rating);
        const halfStar = product.rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
        
        let starsHtml = '';
        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="bi bi-star-fill"></i>';
        }
        if (halfStar) {
            starsHtml += '<i class="bi bi-star-half"></i>';
        }
        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="bi bi-star"></i>';
        }
        
        return `
            <div class="col mb-5">
                <div class="card h-100 product-card">
                    <img class="card-img-top" src="${product.image}" alt="${product.name}" />
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h5 class="fw-bolder">${product.name}</h5>
                            <div class="rating mb-2">
                                ${starsHtml}
                                <span class="ms-1 text-muted small">(${product.reviews})</span>
                            </div>
                            <span class="product-price">${product.price.toFixed(2)} Taka per kg</span>
                        </div>
                    </div>
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-outline-dark mt-auto add-to-cart" href="#" data-id="${product.id}"><i class="bi bi-cart-check-fill"></i></a>
                            <a class="btn btn-dark mt-auto" href="product-detail.html?id=${product.id}">View details</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },
    
    displayFeaturedProducts: function(containerId = 'featured-products', limit = 4) {
        const container = $(`#${containerId}`);
        if (container.length === 0) return;
        
        const featuredProducts = this.getFeaturedProducts(limit);
        let html = '';
        
        featuredProducts.forEach(product => {
            html += this.renderProductCard(product);
        });
        
        container.html(html);
    }
};

$(document).ready(function() {
    PRODUCTS.displayFeaturedProducts();
}); 