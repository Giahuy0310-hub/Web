$(document).ready(function() {
    var displayedProducts = count($productList) 
    var productsPerPage = 6;
    var selectedCategory = json_encode($selectedCategory) 
    var selectedSubcategory =  json_encode($selectedSubcategory) 
    
    $('#load-more').click(function() {
        $.ajax({
            type: 'GET',
            url: 'load_more.php',
            data: {
                displayed: displayedProducts,
                category: selectedCategory,
                subcategory: selectedSubcategory
            },
            success: function(data) {
                $('#product-info .product-container').append(data);
                displayedProducts += productsPerPage;
            }
        });
    });
});
