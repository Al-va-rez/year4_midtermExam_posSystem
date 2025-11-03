<?php
session_start();
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'This is default response'
];

$host = 'localhost';
$db = 'year4_midterm_exam_pos';
$dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

$user = 'root';
$pass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed. . . ']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? null;

switch ($action) {
    case 'createUser':
        $username = trim($input['username'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $password = trim($input['password'] ?? '');
        $confirm_password = trim($input['confirm_password'] ?? '');
        $is_cashier = $input['is_cashier'];

        // check for empty inputs
        if (!empty($username) && !empty($firstname) && !empty($lastname) && !empty($password) && !empty($confirm_password) && isset($is_cashier)) {
            
            // check if username already taken
            $stmt = $pdo->prepare("SELECT COUNT(*) as username_count FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $count = $stmt->fetch();

            if ($count['username_count'] == 0) {
                
                // check if passwords are matching
                if ($password === $confirm_password) {

                    // check password length
                    if (strlen($password) >= 8) {
                        $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                        $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, password, is_cashier, is_suspended) VALUES (?,?,?,?,?,?)");
                        $register = $stmt->execute([$username, $firstname, $lastname, $pass_hash, $is_cashier, false]);

                        if ($register) {
                            $response = [
                                'status' => 'success',
                                'message' => 'Registration successful!'
                            ];
                        } else {
                            $response = [
                                'status' => 'error',
                                'message' => 'Registration failed. . . '
                            ];
                        }

                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Password must be at least 8 characters long'
                        ];
                    }

                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Passwords not the same'
                    ];
                }

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Username already taken'
                ];
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'All fields must be provided'
            ];
        }

        echo json_encode($response);
        break;
    
    case 'createMenuItem':
        $item_name = trim($input['item_name'] ?? '');
        $item_price = trim($input['item_price'] ?? '');

        // check for empty inputs
        if (!empty($item_name) && !empty($item_price)) {
            
            // check if username already taken
            $stmt = $pdo->prepare("SELECT COUNT(*) as item_count FROM menu WHERE item_name = ?");
            $stmt->execute([$item_name]);
            $count = $stmt->fetch();

            if ($count['item_count'] == 0) {
                
                $stmt = $pdo->prepare("INSERT INTO menu (item_name, item_price) VALUES (?,?)");
                $register = $stmt->execute([$item_name, $item_price]);

                if ($register) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Item added to menu!'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Failed to add item to menu...'
                    ];
                }

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Item already in menu'
                ];
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'All fields must be provided'
            ];
        }

        echo json_encode($response);
        break;
    
    case 'recordTransaction':
        $cashier_id = $input['cashier_id'];
        $items = $input['items'];
        $total = $input['total'];
        $amountPaid = $input['amountPaid'];

        // check for empty inputs
        if (count($items) > 0) {
            
            // check if username already taken
            $processTransaction = $pdo->prepare("INSERT INTO transactions (cashier_id, total) VALUES (?,?)");
            $transactionProcessed = $processTransaction->execute([$cashier_id, $total]);

            if ($transactionProcessed) {
                $getLatestTransaction = $pdo->prepare("SELECT * FROM transactions ORDER BY date_added DESC LIMIT 1");
                $getLatestTransaction->execute();

                if ($getLatestTransaction) {
                    $transaction = $getLatestTransaction->fetch();

                    foreach ($items as $item) {
                        $name = $item['name'];
                        $price = $item['price'];
                        $qty = $item['qty'];
                        $subtotal = $item['subtotal'];

                        $saveDetails = $pdo->prepare("INSERT INTO transaction_details (transaction_id, item_name, item_price, item_quantity, item_subtotal) VALUES (?,?,?,?,?)");
                        $saveDetails->execute([$transaction['id'], $name, $price, $qty, $subtotal]);
                    }

                    $response = [
                        'status' => 'success',
                        'message' => 'Transaction recorded'
                    ];
                }
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'Cart is empty.'
            ];
        }

        echo json_encode($response);
        break;
    
    case 'getUsers':
        $search = trim($input['search'] ?? '');

        if (isset($search)) {  // when searching
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR date_added LIKE ?");
            $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
        } else {  // get all
            $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id ASC");
            $stmt->execute();
        }

        $response = [
            'status' => 'success',
            'users' => $stmt->fetchAll()
        ];

        echo json_encode($response);
        break;
    
    case 'getMenu':
        $search = trim($input['search'] ?? '');

        if (isset($search)) {  // when searching
            $stmt = $pdo->prepare("SELECT * FROM menu WHERE item_name LIKE ? OR item_price LIKE ? OR date_added LIKE ?");
            $stmt->execute(["%$search%", "%$search%", "%$search%"]);
        } else {  // get all
            $stmt = $pdo->prepare("SELECT * FROM menu ORDER BY id ASC");
            $stmt->execute();
        }

        $response = [
            'status' => 'success',
            'menu' => $stmt->fetchAll()
        ];

        echo json_encode($response);
        break;
    
    case 'getTransactions':
        $start_date = trim($input['start_date'] ?? '');
        $end_date = trim($input['end_date'] ?? '');

        if (!empty($start_date) && !empty($end_date)) {  // when searching
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE date_added BETWEEN ? AND ?");
            $stmt->execute([$start_date, $end_date]);
        } else {  // get all
            $stmt = $pdo->prepare("SELECT * FROM transactions ORDER BY transactions.id ASC");
            $stmt->execute();
        }

        $response = [
            'status' => 'success',
            'transactions' => $stmt->fetchAll()
        ];

        echo json_encode($response);
        break;
    
    case 'getTransactionDetails':
        $transaction_id = $input['transactionId'];
        $stmt = $pdo->prepare("SELECT * FROM transaction_details WHERE transaction_id = ?");
        $stmt->execute([$transaction_id]);

        $response = [
            'status' => 'success',
            'details' => $stmt->fetchAll()
        ];

        echo json_encode($response);
        break;
    
    case 'updateUser':
        $userId = $input['userId'];
        $username = trim($input['username'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $is_suspended = $input['is_suspended'];

        // check for empty inputs
        if (!empty($username) && !empty($firstname) && !empty($lastname) && isset($is_suspended)) {
            
            // check if new username is already taken
            // *the constraint id != ? in WHERE clause allows the user to keep their username even if other info will be updated
            $stmt = $pdo->prepare("SELECT COUNT(*) as username_count FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $userId]);
            $count = $stmt->fetch();

            if ($count['username_count'] == 0) {
                
                $stmt = $pdo->prepare("UPDATE users SET username = ?, firstname = ?, lastname = ?, is_suspended = ? WHERE id = ?");
                $updateUser = $stmt->execute([$username, $firstname, $lastname, $is_suspended, $userId]);

                if ($updateUser) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Record edited successfully'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Edit operation failed'
                    ];
                }

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Username already taken'
                ];
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'All fields must be provided'
            ];
        }

        echo json_encode($response);
        break;
    
    case 'updateMenuItem':
        $itemId = $input['itemId'];
        $item_name = trim($input['item_name'] ?? '');
        $item_price = trim($input['item_price'] ?? '');

        // check for empty inputs
        if (!empty($item_name) && !empty($item_price)) {
            
            // check if new item name is already taken
            // *the constraint id != ? in WHERE clause allows the item to keep their current name even if other info will be updated
            $stmt = $pdo->prepare("SELECT COUNT(*) as item_count FROM menu WHERE item_name = ? AND id != ?");
            $stmt->execute([$item_name, $itemId]);
            $count = $stmt->fetch();

            if ($count['item_count'] == 0) {
                
                $stmt = $pdo->prepare("UPDATE menu SET item_name = ?, item_price = ? WHERE id = ?");
                $updateUser = $stmt->execute([$item_name, $item_price, $itemId]);

                if ($updateUser) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Item information updated!'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Edit operation failed...'
                    ];
                }

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Item already exists'
                ];
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'All fields must be provided'
            ];
        }

        echo json_encode($response);
        break;
    
    case 'deleteUser':
        $userId = $input['userId'];

        if (isset($userId)) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $deleteOperation = $stmt->execute([$userId]);

            if ($deleteOperation) {
                $response = [
                    'status' => 'success',
                    'message' => 'Record deleted'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Delete operation failed. . . '
                ];
            }
            
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Invalid user ID'
            ];
        }
        
        echo json_encode($response);
        break;
    
    case 'deleteMenuItem':
        $itemId = $input['itemId'];

        if (isset($itemId)) {
            $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
            $deleteOperation = $stmt->execute([$itemId]);

            if ($deleteOperation) {
                $response = [
                    'status' => 'success',
                    'message' => 'Record deleted'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Delete operation failed. . . '
                ];
            }
            
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Invalid user ID'
            ];
        }
        
        echo json_encode($response);
        break;
    
    case 'login':
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        // check for empty inputs
        if (!empty($username) && !empty($password)) {
            
            // check user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND id IS NOT NULL");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {

                // check password
                if (password_verify($password, $user['password'])) {
                    
                    // check if user suspended
                    if (!$user['is_suspended']) {

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['is_cashier'] = $user['is_cashier'];
                        $_SESSION['is_suspended'] = $user['is_suspended'];

                        $response = [
                            'status' => 'success',
                            'message' => 'Login successful.',
                            'is_cashier' => $user['is_cashier'],
                            'is_suspended' => $user['is_suspended']
                        ];

                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Account suspended. Please contact an administrator for further details.'
                        ];
                    }
                    

                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Incorrect password'
                    ];
                }

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'User not yet registered'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'All fields must be provided'
            ];
        }
        
        echo json_encode($response);
        break;
    
    case 'logout':
        session_start();
        session_unset();
        session_destroy();

        $response = [
            'status' => 'success',
            'message' => 'Logging out. . . '
        ];

        ob_clean(); // to redirect to login page after swal timer is complete
        echo json_encode($response);
        break;

    default:
        $response = [
            'status' => 'error',
            'message' => 'Invalid action'
        ];
        echo json_encode($response);
        break;
}

?>