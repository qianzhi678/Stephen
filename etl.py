#Stephen
# Import necessary libraries
import pandas as pd  # For data manipulation and analysis
import sqlite3  # For interacting with SQLite databases

# Load customer and order data from CSV files
customers_df = pd.read_csv('customer.csv')  # Read customer data
orders_df = pd.read_csv('orders.csv')  # Read order data

# Merge order data with customer data based on 'CustomerID' using an inner join
merged_df = pd.merge(orders_df, customers_df, on='CustomerID', how='inner')

# Calculate the total amount for each order (Quantity * Price)
merged_df['TotalAmount'] = merged_df['Quantity'] * merged_df['Price']

# Determine the order status based on the order date
# If the order date starts with '2025-03', status is 'New'; otherwise, it's 'Old'
merged_df['Status'] = merged_df['OrderDate'].apply(lambda d: 'New' if d.startswith('2025-03') else 'Old')

# Filter orders with a total amount greater than 4500 (high-value orders)
high_value_orders = merged_df[merged_df['TotalAmount'] > 4500]

# Connect to the SQLite database (creates the database if it doesn't exist)
conn = sqlite3.connect('ecommerce.db')

# SQL query to create the 'HighValueOrders' table if it doesn't already exist
create_table_query = '''
CREATE TABLE IF NOT EXISTS HighValueOrders (
    OrderID INTEGER,
    CustomerID INTEGER,
    Name TEXT,
    Email TEXT,
    Product TEXT,
    Quantity INTEGER,
    Price REAL,
    OrderDate TEXT,
    TotalAmount REAL,
    Status TEXT
)
'''
# Execute the SQL query to create the table
conn.execute(create_table_query)

# Write the high-value orders data to the 'HighValueOrders' table
# If the table already exists, replace it
high_value_orders.to_sql('HighValueOrders', conn, if_exists='replace', index=False)

# Query all data from the 'HighValueOrders' table and print each row
result = conn.execute('SELECT * FROM HighValueOrders')
for row in result.fetchall():
    print(row)

# Close the database connection
conn.close()

# Print a success message indicating the ETL process is complete
print("ETL process completed successfully!")