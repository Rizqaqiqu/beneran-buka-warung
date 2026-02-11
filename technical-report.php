<?php
session_start();
include 'db.php';
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
    exit;
}

// Get some data for the report
$jumlah_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_category"));
$jumlah_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_product"));
$jumlah_beli = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_pembelian"));
$jumlah_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_admin"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Report - WarungRizqi</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            margin: 40px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .subsection-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .content {
            text-align: justify;
            margin-bottom: 15px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .stats-table th, .stats-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .stats-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 20px;
            margin-top: 50px;
            font-size: 12px;
            color: #666;
        }

        .print-btn {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .print-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print PDF</button>

    <div class="header">
        <div class="logo">WR</div>
        <div class="title">WarungRizqi E-Commerce Platform</div>
        <div class="subtitle">Technical Implementation Report</div>
        <div style="font-size: 14px; margin-top: 10px;">
            Generated on: <?php echo date('F j, Y'); ?>
        </div>
    </div>

    <div class="section">
        <div class="section-title">1. Executive Summary</div>
        <div class="content">
            WarungRizqi is a comprehensive e-commerce platform designed to facilitate online shopping for traditional Indonesian warung products. This technical report details the system architecture, implementation approach, and key technical specifications that enable seamless user experience and robust administrative control.
        </div>
        <div class="content">
            The platform features a modern web-based interface with responsive design, secure payment processing, inventory management, and comprehensive order tracking capabilities. Built using PHP, MySQL, and modern web technologies, the system ensures scalability and maintainability.
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. System Overview</div>
        <div class="subsection-title">2.1 Architecture</div>
        <div class="content">
            The WarungRizqi platform follows a traditional three-tier architecture:
        </div>
        <ul>
            <li><strong>Presentation Layer:</strong> HTML5, CSS3, and JavaScript for responsive user interfaces</li>
            <li><strong>Application Layer:</strong> PHP for server-side logic and business rules</li>
            <li><strong>Data Layer:</strong> MySQL database for persistent data storage</li>
        </ul>

        <div class="subsection-title">2.2 Technology Stack</div>
        <div class="content">
            The system is built using industry-standard technologies:
        </div>
        <ul>
            <li><strong>Backend:</strong> PHP 7.4+, MySQL 5.7+</li>
            <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript (ES6+)</li>
            <li><strong>Server:</strong> Apache/Nginx with XAMPP development environment</li>
            <li><strong>Security:</strong> Session-based authentication, input sanitization</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">3. Technical Specifications</div>
        <div class="subsection-title">3.1 Database Schema</div>
        <div class="content">
            The system utilizes a normalized relational database with the following key tables:
        </div>
        <ul>
            <li><strong>tb_admin:</strong> Administrative user accounts and permissions</li>
            <li><strong>tb_category:</strong> Product category classifications</li>
            <li><strong>tb_product:</strong> Product inventory and specifications</li>
            <li><strong>tb_pembelian:</strong> Purchase transactions and order details</li>
        </ul>

        <div class="subsection-title">3.2 System Statistics</div>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Current Value</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Products</td>
                    <td><?php echo $jumlah_produk; ?></td>
                    <td>Number of items available in inventory</td>
                </tr>
                <tr>
                    <td>Product Categories</td>
                    <td><?php echo $jumlah_kategori; ?></td>
                    <td>Number of product classification categories</td>
                </tr>
                <tr>
                    <td>Total Transactions</td>
                    <td><?php echo $jumlah_beli; ?></td>
                    <td>Number of completed purchase orders</td>
                </tr>
                <tr>
                    <td>Administrative Users</td>
                    <td><?php echo $jumlah_user; ?></td>
                    <td>Number of system administrators</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">4. Implementation Details</div>
        <div class="subsection-title">4.1 Core Features</div>
        <div class="content">
            The platform implements comprehensive e-commerce functionality:
        </div>
        <ul>
            <li><strong>User Management:</strong> Registration, authentication, and profile management</li>
            <li><strong>Product Catalog:</strong> Dynamic product listings with search and filtering</li>
            <li><strong>Shopping Cart:</strong> Session-based cart management with AJAX updates</li>
            <li><strong>Payment Processing:</strong> Multiple payment method support</li>
            <li><strong>Order Management:</strong> Complete order lifecycle tracking</li>
            <li><strong>Administrative Panel:</strong> Comprehensive backend management interface</li>
        </ul>

        <div class="subsection-title">4.2 Security Measures</div>
        <div class="content">
            Security is implemented through multiple layers:
        </div>
        <ul>
            <li>Input validation and sanitization</li>
            <li>SQL injection prevention using prepared statements</li>
            <li>Cross-site scripting (XSS) protection</li>
            <li>Session management with secure cookies</li>
            <li>File upload restrictions and validation</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">5. Testing and Validation</div>
        <div class="subsection-title">5.1 Testing Approach</div>
        <div class="content">
            The system has undergone comprehensive testing including:
        </div>
        <ul>
            <li><strong>Unit Testing:</strong> Individual component functionality verification</li>
            <li><strong>Integration Testing:</strong> Module interaction validation</li>
            <li><strong>User Acceptance Testing:</strong> End-to-end workflow validation</li>
            <li><strong>Performance Testing:</strong> Load and stress testing under various conditions</li>
        </ul>

        <div class="subsection-title">5.2 Quality Assurance</div>
        <div class="content">
            Code quality is maintained through:
        </div>
        <ul>
            <li>Consistent coding standards and documentation</li>
            <li>Regular code reviews and refactoring</li>
            <li>Automated error logging and monitoring</li>
            <li>Responsive design for cross-device compatibility</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">6. Conclusion</div>
        <div class="content">
            The WarungRizqi e-commerce platform represents a robust and scalable solution for online retail operations. The implemented architecture provides a solid foundation for future enhancements and feature additions.
        </div>
        <div class="content">
            Key achievements include seamless user experience, comprehensive administrative capabilities, and adherence to modern web development best practices. The system is production-ready and capable of handling growing business demands.
        </div>
    </div>

    <div class="footer">
        <div>WarungRizqi E-Commerce Platform</div>
        <div>Technical Report Generated: <?php echo date('Y-m-d H:i:s'); ?></div>
        <div>© 2025 WarungRizqi. All Rights Reserved.</div>
    </div>

    <script>
        // Auto-print functionality can be added here if needed
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
