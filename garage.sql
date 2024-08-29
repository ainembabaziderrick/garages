CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    garage_id INT NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (contact),
    UNIQUE (email),
    FOREIGN KEY (garage_id) REFERENCES garages(id)
);

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    garage_id INT NOT NULL,
    number_plate VARCHAR(50) NOT NULL,
    model VARCHAR(255) NOT NULL,
    year YEAR NOT NULL,
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (number_plate),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (garage_id) REFERENCES garages(id)
);

CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unit_cost DECIMAL(10, 2) NOT NULL,
    unit_quantity INT NOT NULL,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES category(id),
    FOREIGN KEY (user_id) REFERENCES users(id)  -- Assuming you have a 'users' table
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    unit_cost INT NOT NULL,
    unit_quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(Id)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sales_id INT NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    balance DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sales_id) REFERENCES sales(id)
);


(SELECT SUM(cart.amount_paid) FROM cart WHERE cart.sales_id = sales.id) as amount_paid

<button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>)">Details</button>

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    garage_id INT,
    user_id INT,
    customer_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (garage_id) REFERENCES garages(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT,
    sales_id INT,
    unit_cost DECIMAL(10, 2),
    unit_quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id),
    FOREIGN KEY (sales_id) REFERENCES sales(id)
);

 $sql = "SELECT cart.unit_cost, cart.unit_quantity, transactions.amount_paid,sales.id, sales.date, accounts.name FROM cart, transactions,sales, accounts WHERE cart.sales_id = sales.id and sales.id = transactions.sales_id and cart.account_id = accounts.id and sales.id = $trackingNo";

 // Get the garage_id from session
$garage_id = $_SESSION['garage_id'];

// Get the date range from GET parameters, if available
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

$date_filter = "";
if ($start_date && $end_date) {
    $date_filter = " AND created_at BETWEEN '$start_date' AND '$end_date'";
}

// Query to get counts and sums
$sql = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE garage_id = $garage_id) as users,
        (SELECT COUNT(*) FROM accounts WHERE garage_id = $garage_id) as services,
        (SELECT COUNT(*) FROM customers WHERE garage_id = $garage_id) as customers,
        (SELECT COUNT(*) FROM sales WHERE garage_id = $garage_id $date_filter) as sales,
        (SELECT SUM(amount_due) FROM sales WHERE garage_id = $garage_id $date_filter) as expected_amount,
        (SELECT SUM(amount_paid) FROM sales WHERE garage_id = $garage_id $date_filter) as paid_amount,
        (SELECT SUM(amount_due) - SUM(amount_paid) FROM sales WHERE garage_id = $garage_id $date_filter) as pending_balance,
        (SELECT COUNT(*) FROM customers WHERE balance > 0 AND garage_id = $garage_id $date_filter) as total_debtors
";

$query = $connection->query($sql);
$row = $query->fetch_assoc();

$userCount = $row['users'];
$servicesCount = $row['services'];
$customerCount = $row['customers'];
$salesCount = $row['sales'];
$expectedAmount = $row['expected_amount'];
$paidAmount = $row['paid_amount'];
$pendingBalance = $row['pending_balance'];
$totalDebtors = $row['total_debtors'];


$sql = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE garage_id = $garage_id) as users,
        (SELECT COUNT(*) FROM accounts WHERE garage_id = $garage_id) as services,
        (SELECT COUNT(*) FROM customers WHERE garage_id = $garage_id) as customers,
        (SELECT COUNT(*) FROM sales WHERE garage_id = $garage_id $date_filter) as sales,
        (SELECT SUM(total) FROM sales WHERE garage_id = $garage_id $date_filter) as expected_amount,
        (SELECT SUM(amount_paid) FROM transactions WHERE garage_id = $garage_id $date_filter) as paid_amount,
        (SELECT (SUM(total) - SUM(amount_paid)) FROM sales
            LEFT JOIN transactions ON sales.sale_id = transactions.sale_id
            WHERE sales.garage_id = $garage_id $date_filter
            GROUP BY sales.sale_id) as pending_balance,
        (SELECT COUNT(*)
            FROM sales
            LEFT JOIN transactions ON sales.sale_id = transactions.sale_id
            WHERE sales.garage_id = $garage_id
            AND sales.total > COALESCE(SUM(transactions.amount_paid), 0)
            $date_filter
            GROUP BY sales.sale_id) as total_debtors
";

$sql = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE garage_id = $garage_id) as users,
        (SELECT COUNT(*) FROM accounts WHERE category_id = $category_id) as services,
        (SELECT COUNT(*) FROM customers WHERE garage_id = $garage_id) as customers,
        (SELECT COUNT(*) FROM sales WHERE garage_id = $garage_id $date_filter) as sales,
        (SELECT SUM(total) FROM sales WHERE garage_id = $garage_id $date_filter) as expected_amount,
        (SELECT SUM(amount_paid) FROM transactions $date_filter) as paid_amount,
        (SELECT COALESCE(SUM(total), 0) - COALESCE(SUM(amount_paid), 0)
            FROM sales
            LEFT JOIN transactions ON sales.id = transactions.sales_id
            WHERE sales.garage_id = $garage_id $date_filter
            GROUP BY sales.id
            ) as pending_balance,
        (SELECT COUNT(*)
            FROM sales
            LEFT JOIN transactions ON sales.sale_id = transactions.sale_id
            WHERE sales.garage_id = $garage_id
            AND sales.total > COALESCE(SUM(transactions.amount_paid), 0)
            $date_filter
            GROUP BY sales.id
            ) as total_debtors
";


 



   SELECT COUNT(customer_id) as debtors FROM sales WHERE date='2024-08-20'  AND total > (SELECT SUM(amount_paid) FROM transactions WHERE sales_id=sales.id);
   


   $sales_id = /* specify the sales ID you want to retrieve */;
$sql = "SELECT *,
            (SELECT name FROM accounts WHERE accounts.id = cart.account_id) AS item_name,
            (SELECT unit_cost FROM cart WHERE cart.sales_id = sales.id LIMIT 1) AS unit_cost,
            (SELECT unit_quantity FROM cart WHERE cart.sales_id = sales.id LIMIT 1) AS unit_quantity,
            (SELECT SUM(amount_paid) FROM transactions WHERE sales_id = sales.id) AS paid,
            (SELECT first_name FROM customers WHERE id = sales.customer_id) AS customer_name,
            (SELECT username FROM users WHERE id = sales.user_id) AS users_name
        FROM sales
        JOIN cart ON cart.sales_id = sales.id
        WHERE sales.id = $sales_id AND garage_id = $garage_id";



"SELECT *,(select name from accounts where accounts.id = cart.account_id) as item_name,
                                                (select unit_cost from cart where cart.sales_id = sales.id limit 1) as unit_cost,
                                                 (select unit_quantity from cart where cart.sales_id = sales.id limit 1) as unit_quantity,
                                                (SELECT SUM(amount_paid) from transactions where sales_id=sales.id)as paid,(SELECT first_name FROM customers where id=sales.customer_id) as customer_name,
                                                (SELECT username FROM users where id=sales.user_id) as users_name from sales, cart WHERE cart.sales_id = sales.id and garage_id = $garage_id";


onclick="openmodaldetails(<?= $row['id'] ?>,'<?= $row['item_name']?>','<?= number_format($row['unit_cost'], 0)?>','<?= $row['unit_quantity']?>','<?= number_format($row['total'],0)?>')"