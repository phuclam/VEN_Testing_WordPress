To use this feature, please do following these steps:
1. Place this folder in your theme directory.
2. Add this line `include TEMPLATEPATH . '/phuc-inc/include.php';` into `functions.php`
3. Setting `Located` option on `Product Category`. See this image : https://ibb.co/W5b2D5f
4. Change your `API_URL` in api.php file.
- A Product has Product Category has option Located is "Vendor Warehouse" is called Vendor Product.
- When customer checkout an order in Frontend which has any Vendor Product, this order will be sent to Laravel System via API.

