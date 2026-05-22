<?php
/**
 * Database Seeder Tool for WriteSphere
 * Supports execution via CLI and an ultra-premium Web Dashboard interface.
 */

// Action routing for AJAX/Browser requests
if (isset($_GET['action']) && $_GET['action'] === 'seed') {
    header('Content-Type: application/json');
    echo executeSeeding();
    exit;
}

// Helper to execute the actual database seeding
function executeSeeding() {
    $logs = [];
    $logs[] = ['type' => 'info', 'message' => 'Initializing database seeder...'];

    // 1. Establish database connection
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $db = "blog_app";

    try {
        $logs[] = ['type' => 'info', 'message' => "Connecting to database on $servername (dbname: $db)..."];
        
        // Attempt connecting using connect.php connection settings
        if (file_exists("app/Models/connect.php")) {
            require "app/Models/connect.php";
            // Check if $conn is defined in connect.php
            if (!isset($conn) || !($conn instanceof PDO)) {
                // Fallback connection if $conn not initialized properly
                $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
            }
        } else {
            $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        }
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $logs[] = ['type' => 'success', 'message' => 'Successfully connected to the database!'];
    } catch (PDOException $e) {
        $logs[] = ['type' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()];
        // Try fallback on port 3306 in case local port was swapped
        try {
            $logs[] = ['type' => 'warning', 'message' => 'Attempting fallback connection on port 3306...'];
            $conn = new PDO("mysql:host=localhost:3306;dbname=$db", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $logs[] = ['type' => 'success', 'message' => 'Connected successfully via fallback port 3306!'];
        } catch (PDOException $ex) {
            $logs[] = ['type' => 'error', 'message' => 'Fallback connection failed as well. Please check if your MySQL server is running and configured correctly.'];
            return json_encode(['status' => 'error', 'message' => 'Connection failed', 'logs' => $logs]);
        }
    }

    try {
        // 2. Disable Foreign Key Checks to allow table truncation
        $logs[] = ['type' => 'info', 'message' => 'Disabling database foreign key checks...'];
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

        // 3. Clear existing tables
        $tables = [
            'replies',
            'comments',
            'blog_post_liked_by',
            'blog_post_media',
            'blog_post_tags',
            'blog_post_category',
            'blog_posts',
            'tags',
            'categories',
            'users'
        ];

        foreach ($tables as $table) {
            try {
                $conn->exec("TRUNCATE TABLE `$table`");
                $logs[] = ['type' => 'success', 'message' => "Truncated table: `$table`"];
            } catch (PDOException $te) {
                $logs[] = ['type' => 'warning', 'message' => "Could not truncate `$table` (might not exist or schema mismatch). Error: " . $te->getMessage()];
            }
        }

        // 4. Seed Users
        $logs[] = ['type' => 'info', 'message' => 'Seeding default user accounts...'];
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@writesphere.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'privilege' => 'admin',
                'bio' => 'Main administrator of the WriteSphere. Tech enthusiast, developer, and chief editor.',
                'gender' => 'male',
                'profile_image' => 'public/uploads/profiles/admin.png'
            ],
            [
                'name' => 'Sarah Connor',
                'email' => 'moderator@writesphere.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'privilege' => 'moderator',
                'bio' => 'Blog editor and moderator. Loves maintaining clean, friendly, and inspiring communities.',
                'gender' => 'female',
                'profile_image' => 'public/uploads/profiles/moderator.png'
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@writesphere.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'privilege' => 'user',
                'bio' => 'Avid traveler, professional photographer, and storyteller. Documenting journeys across the globe.',
                'gender' => 'male',
                'profile_image' => 'public/uploads/profiles/john.png'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@writesphere.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'privilege' => 'user',
                'bio' => 'Culinary chef, organic food advocate, and pastry baker. Sharing delicious recipes and food reviews.',
                'gender' => 'female',
                'profile_image' => 'public/uploads/profiles/jane.png'
            ],
            [
                'name' => 'Alex Miller',
                'email' => 'alex@writesphere.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'privilege' => 'user',
                'bio' => 'Software developer, tech reviewer, and opensource advocate. Writing code tutorials and tech advice.',
                'gender' => 'other',
                'profile_image' => 'public/uploads/profiles/alex.png'
            ]
        ];

        $user_ids = [];
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, privilege, bio, gender, profile_image) VALUES (:name, :email, :password, :privilege, :bio, :gender, :profile_image)");
        foreach ($users as $user) {
            $stmt->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':password' => $user['password'],
                ':privilege' => $user['privilege'],
                ':bio' => $user['bio'],
                ':gender' => $user['gender'],
                ':profile_image' => $user['profile_image']
            ]);
            $userId = $conn->lastInsertId();
            $user_ids[$user['email']] = $userId;
            $logs[] = ['type' => 'success', 'message' => "Created user: {$user['name']} ({$user['privilege']}) [ID: $userId]"];
        }

        // 5. Seed Categories
        $logs[] = ['type' => 'info', 'message' => 'Seeding categories...'];
        $categories = [
            'Technology',
            'Travel',
            'Food & Recipes',
            'Lifestyle & Habits',
            'Business & Startup',
            'Health & Fitness',
            'Science'
        ];

        $category_ids = [];
        $catStmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        foreach ($categories as $cat) {
            $catStmt->execute([':name' => $cat]);
            $catId = $conn->lastInsertId();
            $category_ids[$cat] = $catId;
            $logs[] = ['type' => 'success', 'message' => "Created category: $cat [ID: $catId]"];
        }

        // 6. Seed Tags
        $logs[] = ['type' => 'info', 'message' => 'Seeding tags...'];
        $tags = [
            '#php',
            '#webdev',
            '#coding',
            '#travelvlog',
            '#photography',
            '#foodie',
            '#recipes',
            '#mindfulness',
            '#productivity',
            '#startup',
            '#marketing',
            '#health',
            '#ai',
            '#tech'
        ];

        $tag_ids = [];
        $tagStmt = $conn->prepare("INSERT INTO tags (name) VALUES (:name)");
        foreach ($tags as $tag) {
            $tagStmt->execute([':name' => $tag]);
            $tagId = $conn->lastInsertId();
            $tag_ids[$tag] = $tagId;
            $logs[] = ['type' => 'success', 'message' => "Created tag: $tag [ID: $tagId]"];
        }

        // 7. Seed Blog Posts
        $logs[] = ['type' => 'info', 'message' => 'Seeding blog posts...'];
        
        $posts_data = [
            [
                'author_email' => 'alex@writesphere.com',
                'title' => 'The Future of Web Development with PHP 8.4',
                'content' => "<h2>The PHP Evolution</h2><p>PHP has come an incredibly long way over the last few years. With the release of PHP 8.4, we see some of the most exciting additions to the language yet. Among the highlights are <strong>Property Hooks</strong>, which will fundamentally change how we write getters and setters in PHP, making our models significantly cleaner and less verbose.</p><h3>What are Property Hooks?</h3><p>Property hooks allow developers to define custom logic for property access and modification directly in the property declaration. This removes the boilerplate code of traditional methods.</p><pre><code>class User {\n    public string \$name {\n        set => trim(ucfirst(\$value));\n    }\n}</code></pre><p>In this post, we will explore how this feature operates under the hood, compare it with magic methods, and evaluate the performance impact of this highly anticipated update.</p>",
                'status' => 'published',
                'categories' => ['Technology'],
                'tags' => ['#php', '#webdev', '#coding', '#tech'],
                'media_path' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800',
                'likes' => 12
            ],
            [
                'author_email' => 'john@writesphere.com',
                'title' => '10 Hidden Gems in Kyoto You Must Visit',
                'content' => "<h2>Discovering Off-the-Beaten-Path Kyoto</h2><p>Kyoto is worldwide famous for its magnificent shrines and temples, such as Fushimi Inari and Kinkaku-ji. However, during peak tourist seasons, these spots can get incredibly crowded, taking away from the serene atmosphere Kyoto is built for.</p><p>In this guide, we reveal <strong>10 hidden gems</strong> that remain relatively untouched by crowds:</p><ul><li><strong>Gio-ji Temple:</strong> A tiny, beautiful moss garden hidden deep in the bamboo forests of Arashiyama.</li><li><strong>Honen-in Temple:</strong> A peaceful sanctuary near the Philosopher's Path, offering tranquil nature walks.</li><li><strong>Otagi Nenbutsu-ji:</strong> Famous for its 1,200 whimsical stone statues, each with a different facial expression!</li></ul><p>Explore these spots to experience Kyoto’s traditional charm and peaceful beauty in absolute tranquility.</p>",
                'status' => 'published',
                'categories' => ['Travel'],
                'tags' => ['#travelvlog', '#photography'],
                'media_path' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800',
                'likes' => 25
            ],
            [
                'author_email' => 'jane@writesphere.com',
                'title' => 'Mastering the Perfect Sourdough Bread at Home',
                'content' => "<h2>The Art and Science of Fermentation</h2><p>Baking sourdough at home is more than just cooking; it is a therapeutic ritual of nurturing wild yeast and lactic acid bacteria. If you have been intimidated by sourdough baking, don't worry! This comprehensive guide simplifies the science behind hydration, gluten structure, and fermentation.</p><h3>Key Sourdough Steps:</h3><ol><li><strong>Feeding the Starter:</strong> Ensure your starter is active, bubbly, and doubles in size before mixing.</li><li><strong>Autolyse:</strong> Let flour and water rest to build gluten naturally before adding salt and starter.</li><li><strong>Bulk Fermentation:</strong> Perform gentle stretch-and-folds every 30 minutes to develop structure.</li><li><strong>Cold Retard:</strong> Proof in the fridge overnight to develop a rich, tangy flavor profile.</li><li><strong>Baking:</strong> Use a preheated Dutch oven to achieve that perfect crispy, blistered crust!</li></ol><p>Follow this detailed method and you will be slicing into a beautiful open-crumb loaf in no time.</p>",
                'status' => 'published',
                'categories' => ['Food & Recipes'],
                'tags' => ['#foodie', '#recipes'],
                'media_path' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=800',
                'likes' => 31
            ],
            [
                'author_email' => 'jane@writesphere.com',
                'title' => '5 Mindfulness Habits for a Productive Workday',
                'content' => "<h2>Cultivating Calm Amidst the Chaos</h2><p>With endless notifications, emails, and meetings, staying focused and calm throughout the workday is a modern superpower. Productivity isn't about working yourself to exhaustion; it's about managing your mental energy effectively.</p><p>Here are <strong>5 simple mindfulness habits</strong> you can implement today to increase focus and reduce stress:</p><ul><li><strong>Start with a 5-minute tech-free morning:</strong> Drink your tea or coffee without checking your phone first thing.</li><li><strong>Single-tasking:</strong> Dedicate block periods of time to a single activity. Close unnecessary tabs!</li><li><strong>The 20-20-20 Rule:</strong> Every 20 minutes, look at an object 20 feet away for 20 seconds to give your eyes and mind a break.</li><li><strong>Mindful Breathing:</strong> Take three deep breaths before replying to a challenging email or starting a meeting.</li><li><strong>End-of-day reflection:</strong> Write down three things you accomplished to shut down your brain for the evening.</li></ul>",
                'status' => 'published',
                'categories' => ['Lifestyle & Habits', 'Health & Fitness'],
                'tags' => ['#mindfulness', '#productivity', '#health'],
                'media_path' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=800',
                'likes' => 18
            ],
            [
                'author_email' => 'alex@writesphere.com',
                'title' => 'Demystifying Artificial Intelligence: Beyond the Hype',
                'content' => "<h2>Understanding Large Language Models (LLMs)</h2><p>AI is the biggest buzzword in the technology industry today. From automated customer support to generative artwork and coding companions, AI tools are proliferating at an unprecedented speed. But what exactly is AI, and how do modern Large Language Models operate under the hood?</p><h3>How Neural Networks Process Information</h3><p>At their core, modern LLMs are advanced statistical predictors. They do not 'think' or 'reason' the way humans do; instead, they analyze mathematical weight matrices to identify the most probable sequence of words to follow a given input.</p><p>We will break down concepts like transformers, parameters, fine-tuning, and neural weights in simple, accessible terms so you can understand the technology shaping our future.</p>",
                'status' => 'published',
                'categories' => ['Technology', 'Science'],
                'tags' => ['#ai', '#tech', '#coding'],
                'media_path' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800',
                'likes' => 45
            ],
            [
                'author_email' => 'admin@writesphere.com',
                'title' => 'Bootstrap Your Startup: Lessons from a Solo Founder',
                'content' => "<h2>The Power of Constraints</h2><p>Building a successful business doesn't require millions in venture capital. In fact, raising money too early often distracts from the core mission: building a product that customers love and are willing to pay for. Bootstrapping forces founders to focus on profitability, cost control, and deep customer alignment.</p><h3>Key Rules for Bootstrappers:</h3><ul><li><strong>Build a Minimum Viable Product (MVP):</strong> Launch with only essential core features. Iterate based on real customer usage.</li><li><strong>Stay extremely lean:</strong> Keep your monthly operating costs to an absolute minimum. Use open source!</li><li><strong>Focus on organic marketing:</strong> Use content marketing, SEO, and developer communities to gain traction.</li><li><strong>Charge from day one:</strong> Validation means credit card details, not newsletter sign-ups.</li></ul><p>By embracing bootstrapping, you maintain 100% control of your equity and build a resilient business that can survive any economic cycle.</p>",
                'status' => 'published',
                'categories' => ['Business & Startup'],
                'tags' => ['#startup', '#marketing', '#productivity'],
                'media_path' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800',
                'likes' => 29
            ],
            [
                'author_email' => 'jane@writesphere.com',
                'title' => 'The Ultimate Meal Prep Guide for Busy Professionals',
                'content' => "<h2>Eating Healthy Made Convenient</h2><p>When you are exhausted after a long workday, cooking a nutritious meal from scratch can feel impossible, leading to takeout orders and processed food. The solution? Strategic meal prepping. Dedicating just 2.5 hours on a Sunday can guarantee you have delicious, nourishing meals ready for the entire week.</p><h3>Our Meal Prep Strategy:</h3><p>We focus on multi-use ingredients: roasting a large batch of seasonal vegetables, cooking grains, and prepping versatile proteins like grilled chicken, tofu, or black beans. We then store them in airtight glass containers to build healthy bowls with various homemade dressings throughout the week.</p><p>We share our top shopping list, container recommendations, and step-by-step Sunday timeline to keep your food fresh and exciting!</p>",
                'status' => 'published',
                'categories' => ['Health & Fitness', 'Food & Recipes'],
                'tags' => ['#health', '#foodie', '#recipes'],
                'media_path' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800',
                'likes' => 15
            ],
            [
                'author_email' => 'john@writesphere.com',
                'title' => 'A Photographer Guide to Golden Hour Lighting',
                'content' => "<h2>Chasing the Golden Light</h2><p>There is a magical window of time just after sunrise and right before sunset when the sun is low in the sky, casting long, soft shadows and bathing the landscape in a warm, amber glow. This is the <strong>Golden Hour</strong>, and it is every photographer's favorite time of day.</p><h3>Golden Hour Shooting Tips:</h3><ul><li><strong>Shoot Underexposed:</strong> Pull down your exposure slightly to preserve the rich, golden highlight details.</li><li><strong>Embrace Rim Lighting:</strong> Position your subject directly between you and the sun to create a beautiful, glowing outline.</li><li><strong>Shoot RAW:</strong> Golden hour lighting creates heavy contrast. Shooting RAW gives you the necessary dynamic range to recover shadows in post-processing.</li></ul><p>We discuss how to plan around weather, use scouting apps, and adapt your camera settings to capture stunning golden images.</p>",
                'status' => 'published',
                'categories' => ['Travel', 'Lifestyle & Habits'],
                'tags' => ['#photography', '#travelvlog'],
                'media_path' => 'https://images.unsplash.com/photo-1472214222541-d510753a8707?w=800',
                'likes' => 22
            ],
            [
                'author_email' => 'admin@writesphere.com',
                'title' => 'Upcoming Blog App Redesign Plans',
                'content' => "<h2>Sneak Peek of our Brand New Interface!</h2><p>This is a draft preview of the upcoming design system we are planning to implement for WriteSphere. We will be adding dark mode toggles, interactive card animations, glassmorphism headers, and streamlined rich content views.</p><p>Stay tuned for more updates as we roll out these exciting changes next month!</p>",
                'status' => 'draft',
                'categories' => ['Technology'],
                'tags' => ['#webdev', '#tech'],
                'media_path' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800',
                'likes' => 0
            ],
            [
                'author_email' => 'alex@writesphere.com',
                'title' => 'An Introduction to Docker Containerization',
                'content' => "<h2>But it works on my machine!</h2><p>How many times have you heard or said this phrase? Docker solves the classic environment mismatch problem by packaging applications along with their complete dependencies, system libraries, and configurations into self-contained lightweight containers.</p><p>We walk through writing your very first Dockerfile, launching containers, and understanding key differences between virtual machines and containers.</p>",
                'status' => 'scheduled',
                'categories' => ['Technology'],
                'tags' => ['#coding', '#tech', '#webdev'],
                'media_path' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?w=800',
                'likes' => 0
            ]
        ];

        $post_ids = [];
        
        $postStmt = $conn->prepare("
            INSERT INTO blog_posts (user_id, title, content, status, likes, scheduled_at, published_at, created_at, updated_at) 
            VALUES (:user_id, :title, :content, :status, :likes, :scheduled_at, :published_at, :created_at, :updated_at)
        ");

        $mediaStmt = $conn->prepare("
            INSERT INTO blog_post_media (blog_post_id, file_path, file_type, created_at, updated_at) 
            VALUES (:blog_post_id, :file_path, 'Image', NOW(), NOW())
        ");

        $pivotCatStmt = $conn->prepare("
            INSERT INTO blog_post_category (blog_post_id, category_id) 
            VALUES (:blog_post_id, :category_id)
        ");

        $pivotTagStmt = $conn->prepare("
            INSERT INTO blog_post_tags (blog_post_id, tag_id) 
            VALUES (:blog_post_id, :tag_id)
        ");

        $p_idx = 0;
        foreach ($posts_data as $p) {
            $authorId = $user_ids[$p['author_email']];
            
            // Generate dates
            $created = date('Y-m-d H:i:s', strtotime("-$p_idx days"));
            $published = $p['status'] === 'published' ? $created : null;
            $scheduled = $p['status'] === 'scheduled' ? date('Y-m-d H:i:s', strtotime("+2 days")) : null;

            $postStmt->execute([
                ':user_id' => $authorId,
                ':title' => $p['title'],
                ':content' => $p['content'],
                ':status' => $p['status'],
                ':likes' => $p['likes'],
                ':scheduled_at' => $scheduled,
                ':published_at' => $published,
                ':created_at' => $created,
                ':updated_at' => $created
            ]);

            $postId = $conn->lastInsertId();
            $post_ids[] = $postId;
            $logs[] = ['type' => 'success', 'message' => "Created blog post: \"{$p['title']}\" [ID: $postId]"];

            // Seed Media link
            if (!empty($p['media_path'])) {
                $mediaStmt->execute([
                    ':blog_post_id' => $postId,
                    ':file_path' => $p['media_path']
                ]);
                $logs[] = ['type' => 'success', 'message' => "  Associated cover image for post [ID: $postId]"];
            }

            // Pivot Categories
            foreach ($p['categories'] as $cName) {
                if (isset($category_ids[$cName])) {
                    $pivotCatStmt->execute([
                        ':blog_post_id' => $postId,
                        ':category_id' => $category_ids[$cName]
                    ]);
                }
            }

            // Pivot Tags
            foreach ($p['tags'] as $tName) {
                if (isset($tag_ids[$tName])) {
                    $pivotTagStmt->execute([
                        ':blog_post_id' => $postId,
                        ':tag_id' => $tag_ids[$tName]
                    ]);
                }
            }
            $p_idx++;
        }

        // 8. Seed Comments
        $logs[] = ['type' => 'info', 'message' => 'Seeding comments...'];
        
        $comments_data = [
            [
                'post_idx' => 0, // PHP 8.4 Post
                'author_email' => 'john@writesphere.com',
                'content' => 'This is an incredibly detailed write-up! I was wondering how property hooks perform compared to magic methods. Thanks for clarifying!',
                'replies' => [
                    [
                        'author_email' => 'alex@writesphere.com',
                        'content' => 'Thank you! Property hooks are actually faster than traditional PHP magic __get/__set calls because they are compiled directly as methods.'
                    ],
                    [
                        'author_email' => 'admin@writesphere.com',
                        'content' => 'Absolutely! This is one of the biggest wins for clean architecture in PHP in years.'
                    ]
                ]
            ],
            [
                'post_idx' => 0, // PHP 8.4 Post
                'author_email' => 'jane@writesphere.com',
                'content' => 'Writing getters and setters was always my least favorite part of OOP in PHP. This looks like a massive quality of life upgrade!',
                'replies' => []
            ],
            [
                'post_idx' => 1, // Kyoto Post
                'author_email' => 'jane@writesphere.com',
                'content' => 'Wow, Honen-in looks gorgeous and so peaceful! I am visiting Japan next March and I am adding this to my itinerary right away.',
                'replies' => [
                    [
                        'author_email' => 'john@writesphere.com',
                        'content' => 'You will love it! March is a great time, especially with the early plum and cherry blossoms starting to emerge.'
                    ]
                ]
            ],
            [
                'post_idx' => 1, // Kyoto Post
                'author_email' => 'admin@writesphere.com',
                'content' => 'The stone statues in Otagi Nenbutsu-ji are spectacular. Great photography in this post, John!',
                'replies' => []
            ],
            [
                'post_idx' => 2, // Sourdough Bread
                'author_email' => 'john@writesphere.com',
                'content' => 'My sourdough loaves always turn out a bit flat with a dense crumb. I think I am letting them over-proof during bulk fermentation. I will try your timing!',
                'replies' => [
                    [
                        'author_email' => 'jane@writesphere.com',
                        'content' => 'Yes, over-proofing is the most common culprit! Try reducing bulk fermentation by 30 minutes, or watch the temperature of your kitchen.'
                    ]
                ]
            ],
            [
                'post_idx' => 3, // Mindfulness Habits
                'author_email' => 'alex@writesphere.com',
                'content' => 'The 20-20-20 rule has saved my eyes. I have a timer set on my desk now. Highly recommend this to all fellow programmers!',
                'replies' => []
            ],
            [
                'post_idx' => 4, // AI Post
                'author_email' => 'moderator@writesphere.com',
                'content' => 'Such a grounded explanation! It is refreshing to read an article that avoids both extreme hype and extreme alarmism. Great job, Alex.',
                'replies' => [
                    [
                        'author_email' => 'alex@writesphere.com',
                        'content' => 'Exactly, understanding the statistical nature of LLMs is key to using them productively rather than fearing them.'
                    ]
                ]
            ],
            [
                'post_idx' => 5, // Bootstrapping
                'author_email' => 'alex@writesphere.com',
                'content' => 'Charging from day one is the ultimate validation. If people do not want to pull out their wallet, you have built a hobby, not a business.',
                'replies' => []
            ],
            [
                'post_idx' => 5, // Bootstrapping
                'author_email' => 'john@writesphere.com',
                'content' => 'This is exactly the inspiration I needed today. Keeping operational costs low allows you to survive long enough to find product-market fit.',
                'replies' => []
            ]
        ];

        $commentStmt = $conn->prepare("
            INSERT INTO comments (blog_post_id, user_id, content, created_at) 
            VALUES (:blog_post_id, :user_id, :content, :created_at)
        ");

        $replyStmt = $conn->prepare("
            INSERT INTO replies (comment_id, user_id, content, created_at) 
            VALUES (:comment_id, :user_id, :content, NOW())
        ");

        $c_idx = 0;
        foreach ($comments_data as $c) {
            $postId = $post_ids[$c['post_idx']];
            $commAuthorId = $user_ids[$c['author_email']];
            $createdDate = date('Y-m-d H:i:s', strtotime("-$c_idx hours"));

            $commentStmt->execute([
                ':blog_post_id' => $postId,
                ':user_id' => $commAuthorId,
                ':content' => $c['content'],
                ':created_at' => $createdDate
            ]);

            $commentId = $conn->lastInsertId();
            $logs[] = ['type' => 'success', 'message' => "Created comment [ID: $commentId] on post [ID: $postId]"];

            // Seed replies
            foreach ($c['replies'] as $rep) {
                $repAuthorId = $user_ids[$rep['author_email']];
                $replyStmt->execute([
                    ':comment_id' => $commentId,
                    ':user_id' => $repAuthorId,
                    ':content' => $rep['content']
                ]);
                $replyId = $conn->lastInsertId();
                $logs[] = ['type' => 'success', 'message' => "  Created reply [ID: $replyId] by {$rep['author_email']} on comment [ID: $commentId]"];
            }
            $c_idx++;
        }

        // 9. Seed Likes
        $logs[] = ['type' => 'info', 'message' => 'Seeding post likes relationships...'];
        
        $likeStmt = $conn->prepare("
            INSERT INTO blog_post_liked_by (blog_post_id, user_id) 
            VALUES (:blog_post_id, :user_id)
        ");

        // Let's seed random likes
        $user_ids_list = array_values($user_ids);
        foreach ($post_ids as $pId) {
            // Get post actual likes count
            $likes_count_stmt = $conn->prepare("SELECT likes FROM blog_posts WHERE id = :id");
            $likes_count_stmt->execute([':id' => $pId]);
            $like_target = (int)$likes_count_stmt->fetchColumn();

            if ($like_target > 0) {
                // Select random users to like this post
                $likers = $user_ids_list;
                shuffle($likers);
                $slice_limit = min($like_target, count($user_ids_list));
                $selected_likers = array_slice($likers, 0, $slice_limit);

                foreach ($selected_likers as $lUserId) {
                    try {
                        $likeStmt->execute([
                            ':blog_post_id' => $pId,
                            ':user_id' => $lUserId
                        ]);
                    } catch (PDOException $le) {
                        // ignore duplicate entry if any
                    }
                }
                $logs[] = ['type' => 'success', 'message' => "Seeded " . count($selected_likers) . " user likes for post [ID: $pId]"];
            }
        }

        // Re-enable Foreign Key Checks
        $logs[] = ['type' => 'info', 'message' => 'Re-enabling database foreign key checks...'];
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

        $logs[] = ['type' => 'success', 'message' => '=================================================='];
        $logs[] = ['type' => 'success', 'message' => 'DATABASE SEEDING COMPLETED SUCCESSFULLY! 🎉'];
        $logs[] = ['type' => 'success', 'message' => 'All tables populated with high-quality rich content.'];
        $logs[] = ['type' => 'success', 'message' => '=================================================='];

        return json_encode([
            'status' => 'success',
            'message' => 'Seeding completed successfully!',
            'logs' => $logs
        ]);

    } catch (Exception $e) {
        // Safe check to always restore foreign keys in case of error
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $ex) {}

        $logs[] = ['type' => 'error', 'message' => 'Critical error during seeding: ' . $e->getMessage()];
        return json_encode([
            'status' => 'error',
            'message' => 'Seeding aborted due to an error.',
            'logs' => $logs
        ]);
    }
}

// ----------------------------------------------------
// CLI Execution Handler
// ----------------------------------------------------
if (php_sapi_name() === 'cli') {
    echo "==================================================\n";
    echo "         WriteSphere DATABASE CLI SEEDER TOOL         \n";
    echo "==================================================\n";
    
    $res = json_decode(executeSeeding(), true);
    
    foreach ($res['logs'] as $log) {
        $prefix = "[INFO] ";
        if ($log['type'] === 'success') $prefix = "[SUCCESS] ";
        if ($log['type'] === 'warning') $prefix = "[WARNING] ";
        if ($log['type'] === 'error') $prefix = "[ERROR] ";
        echo $prefix . $log['message'] . "\n";
    }
    
    if ($res['status'] === 'success') {
        echo "\nSeeding successful! You can now log in with the following default accounts:\n";
        echo "  - Admin: admin@writesphere.com (Password: password123)\n";
        echo "  - User: john@writesphere.com (Password: password123)\n";
        echo "  - User: jane@writesphere.com (Password: password123)\n";
    } else {
        echo "\nSeeding failed. Please check the logs above.\n";
    }
    exit(0);
}

// ----------------------------------------------------
// Web Interface (HTML/CSS) Handler
// ----------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeder - WriteSphere</title>
    
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Stunning Vanilla CSS -->
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-indigo: #6366f1;
            --accent-violet: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --info-color: #3b82f6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1.5rem;
            overflow-x: hidden;
            position: relative;
        }

        /* Glowing background blobs */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
            top: -100px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.12), transparent 70%);
            bottom: -200px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .container {
            width: 100%;
            max-width: 900px;
            z-index: 10;
            position: relative;
        }

        /* Glassmorphism Panel */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.05em;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #a5b4fc, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 1.05rem;
            font-weight: 400;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.2);
            background: rgba(15, 23, 42, 0.6);
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent-indigo);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Controls Section */
        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .btn-seed {
            background: linear-gradient(135deg, var(--accent-indigo) 0%, var(--accent-violet) 100%);
            color: white;
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .btn-seed::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-seed:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.6);
        }

        .btn-seed:hover::before {
            transform: translateX(100%);
        }

        .btn-seed:active {
            transform: translateY(1px);
        }

        .btn-seed:disabled {
            background: #334155;
            color: #64748b;
            box-shadow: none;
            cursor: not-allowed;
        }

        /* Progress Bar */
        .progress-container {
            width: 100%;
            height: 8px;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 9999px;
            overflow: hidden;
            display: none;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(to right, var(--accent-indigo), var(--accent-violet));
            border-radius: 9999px;
            transition: width 0.4s ease;
            position: relative;
        }

        /* Styled Terminal Console */
        .terminal {
            background: #090d16;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 320px;
            margin-bottom: 2rem;
        }

        .terminal-header {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .terminal-dots {
            display: flex;
            gap: 0.4rem;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-red { background: var(--error-color); }
        .dot-yellow { background: var(--warning-color); }
        .dot-green { background: var(--success-color); }

        .terminal-title {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }

        .terminal-body {
            padding: 1.25rem;
            overflow-y: auto;
            flex-grow: 1;
            font-family: 'JetBrains Mono', 'Consolas', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            scroll-behavior: smooth;
        }

        .log-entry {
            display: flex;
            gap: 0.5rem;
            animation: fadeIn 0.15s ease forwards;
        }

        .log-time {
            color: #475569;
            user-select: none;
        }

        .log-text-info { color: #94a3b8; }
        .log-text-success { color: var(--success-color); }
        .log-text-warning { color: var(--warning-color); }
        .log-text-error { color: var(--error-color); font-weight: 600; }

        /* Credentials Box */
        .credentials-card {
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            display: none;
            animation: slideUp 0.4s ease forwards;
        }

        .credentials-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent-indigo);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .credentials-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-top: 0.75rem;
        }

        .credential-item {
            background: rgba(15, 23, 42, 0.5);
            padding: 0.85rem 1rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.02);
            font-size: 0.85rem;
        }

        .credential-role {
            font-weight: 700;
            color: #a5b4fc;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .credential-detail {
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            margin-top: 0.2rem;
        }

        .credential-detail span {
            color: var(--text-secondary);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(2px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Scrollbar for Terminal */
        .terminal-body::-webkit-scrollbar {
            width: 8px;
        }
        .terminal-body::-webkit-scrollbar-track {
            background: transparent;
        }
        .terminal-body::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 99px;
        }
        .terminal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="glass-panel">
            <header>
                <h1>Database Seeder Tool</h1>
                <p class="subtitle">Vanilla PHP MVC Blog Application Seed Agent</p>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div class="stat-label">User Accounts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">10</div>
                    <div class="stat-label">Rich Articles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">21</div>
                    <div class="stat-label">Categories & Tags</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Likes & Comments</div>
                </div>
            </div>

            <!-- Controls -->
            <div class="controls">
                <button id="btnSeed" class="btn-seed" onclick="startSeeding()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    Run Database Seeder
                </button>
                
                <div id="progressContainer" class="progress-container">
                    <div id="progressBar" class="progress-bar"></div>
                </div>
            </div>

            <!-- Console Log -->
            <div class="terminal">
                <div class="terminal-header">
                    <div class="terminal-dots">
                        <div class="terminal-dot dot-red"></div>
                        <div class="terminal-dot dot-yellow"></div>
                        <div class="terminal-dot dot-green"></div>
                    </div>
                    <div class="terminal-title">seeder_output.log</div>
                    <div style="width: 42px;"></div>
                </div>
                <div id="terminalBody" class="terminal-body">
                    <div class="log-entry">
                        <span class="log-time">[00:00:00]</span>
                        <span class="log-text-info">Ready to seed. Click "Run Database Seeder" to start.</span>
                    </div>
                </div>
            </div>

            <!-- Access Credentials Info Box -->
            <div id="credentialsCard" class="credentials-card">
                <div class="credentials-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Seeded User Credentials
                </div>
                <p class="subtitle" style="font-size: 0.9rem; margin-bottom: 0.5rem;">Use these test credentials to explore different roles and permissions:</p>
                <div class="credentials-list">
                    <div class="credential-item">
                        <div class="credential-role">Administrator</div>
                        <div class="credential-detail"><span>Email:</span> admin@writesphere.com</div>
                        <div class="credential-detail"><span>Pass:</span> password123</div>
                    </div>
                    <div class="credential-item">
                        <div class="credential-role">Moderator</div>
                        <div class="credential-detail"><span>Email:</span> moderator@writesphere.com</div>
                        <div class="credential-detail"><span>Pass:</span> password123</div>
                    </div>
                    <div class="credential-item">
                        <div class="credential-role">Standard User (Blogger)</div>
                        <div class="credential-detail"><span>Email:</span> john@writesphere.com</div>
                        <div class="credential-detail"><span>Pass:</span> password123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Javascript Logic -->
    <script>
        function getTimeString() {
            const now = new Date();
            return `[${now.toTimeString().split(' ')[0]}]`;
        }

        function appendLog(type, text) {
            const body = document.getElementById('terminalBody');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            
            const time = document.createElement('span');
            time.className = 'log-time';
            time.textContent = getTimeString();
            
            const content = document.createElement('span');
            content.className = `log-text-${type}`;
            content.textContent = text;
            
            entry.appendChild(time);
            entry.appendChild(content);
            body.appendChild(entry);
            
            // Auto scroll to bottom
            body.scrollTop = body.scrollHeight;
        }

        async function startSeeding() {
            const btn = document.getElementById('btnSeed');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const credentialsCard = document.getElementById('credentialsCard');
            const body = document.getElementById('terminalBody');
            
            // Reset UI
            body.innerHTML = '';
            credentialsCard.style.display = 'none';
            btn.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '10%';
            
            appendLog('info', 'Triggering database seed operations...');
            
            try {
                // Simulate progressive loading
                let progressInterval = setInterval(() => {
                    let currentWidth = parseFloat(progressBar.style.width);
                    if (currentWidth < 85) {
                        progressBar.style.width = (currentWidth + 15) + '%';
                    }
                }, 400);

                // Fetch seeding execution API
                const response = await fetch('seeder.php?action=seed');
                clearInterval(progressInterval);
                
                progressBar.style.width = '95%';
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Render the received logs
                if (data.logs && Array.isArray(data.logs)) {
                    data.logs.forEach(log => {
                        appendLog(log.type, log.message);
                    });
                }
                
                if (data.status === 'success') {
                    progressBar.style.width = '100%';
                    appendLog('success', 'Database seed completed successfully!');
                    credentialsCard.style.display = 'block';
                } else {
                    progressBar.style.width = '0%';
                    appendLog('error', 'Seeding failed. See logs above for details.');
                }
            } catch (error) {
                appendLog('error', `Seeding error: ${error.message}`);
                appendLog('error', 'Please make sure your database server is running on the specified port.');
                progressBar.style.width = '0%';
            } finally {
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
