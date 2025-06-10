<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SporTTrackers - Sports Health Tracker</title>
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>
<body>
  <div class="app-container">
    <!-- Sidebar for desktop -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
            <path d="M6.5 6.5h11"></path>
            <path d="M6.5 12h11"></path>
            <path d="m6.5 17.5 4-5.5 4 5.5"></path>
          </svg>
          <h1>SporTTrackers</h1>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <ul>
          <li>
            <a href="#" class="nav-item active" data-page="dashboardPage">
              <i class="fas fa-chart-line"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="workoutsPage">
              <i class="fas fa-dumbbell"></i>
              <span>Workouts</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="healthMetricsPage">
              <i class="fas fa-heartbeat"></i>
              <span>Health Metrics</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="progressPage">
              <i class="fas fa-chart-bar"></i>
              <span>Progress</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="profilePage">
              <i class="fas fa-user"></i>
              <span>Profile</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="settingsPage">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="sidebar-footer">
        <button class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Mobile header -->
    <header class="mobile-header">
      <button class="menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
      </button>
      <div class="mobile-user">
        <div class="avatar">JD</div>
      </div>
    </header>
    
    <!-- Mobile drawer menu -->
    <div class="mobile-drawer" id="mobileDrawer">
      <div class="drawer-header">
        <div class="logo">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
            <path d="M6.5 6.5h11"></path>
            <path d="M6.5 12h11"></path>
            <path d="m6.5 17.5 4-5.5 4 5.5"></path>
          </svg>
          <h1>SporTTrackers</h1>
        </div>
        <button class="close-menu" id="closeMenu">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <nav class="drawer-nav">
        <ul>
          <li>
            <a href="#" class="nav-item active" data-page="dashboardPage">
              <i class="fas fa-chart-line"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="workoutsPage">
              <i class="fas fa-dumbbell"></i>
              <span>Workouts</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="healthMetricsPage">
              <i class="fas fa-heartbeat"></i>
              <span>Health Metrics</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="progressPage">
              <i class="fas fa-chart-bar"></i>
              <span>Progress</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="profilePage">
              <i class="fas fa-user"></i>
              <span>Profile</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-item" data-page="settingsPage">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="drawer-footer">
        <button class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </div>
    </div>
    
    <!-- Mobile bottom navigation -->
    <nav class="mobile-bottom-nav">
      <a href="#" class="bottom-nav-item active" data-page="dashboardPage">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
      </a>
      <a href="#" class="bottom-nav-item" data-page="healthMetricsPage">
        <i class="fas fa-heartbeat"></i>
        <span>Health</span>
      </a>
      <a href="#" class="bottom-nav-item" data-page="workoutsPage">
        <div class="nav-plus-circle">
          <i class="fas fa-plus"></i>
        </div>
        <span>New</span>
      </a>
      <a href="#" class="bottom-nav-item" data-page="progressPage">
        <i class="fas fa-chart-bar"></i>
        <span>Progress</span>
      </a>
      <a href="#" class="bottom-nav-item" data-page="settingsPage">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
    </nav>

    <!-- Page content container -->
    <main class="content-container">
      <!-- Auth Page content -->
      <div class="page-content" id="authPage">
        <div class="auth-container">
          <div class="auth-hero">
            <h1>Track Your Fitness Journey with SporTTrackers</h1>
            <p>Monitor your workouts, track health metrics, and achieve your fitness goals with our comprehensive sports health tracker.</p>
            
            <div class="features-grid">
              <div class="feature-card">
                <i class="fas fa-walking"></i>
                <h3>Activity Tracking</h3>
                <p>Track steps, calories, and active minutes daily</p>
              </div>
              <div class="feature-card">
                <i class="fas fa-heartbeat"></i>
                <h3>Health Metrics</h3>
                <p>Monitor heart rate, weight, and sleep</p>
              </div>
              <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Progress Insights</h3>
                <p>Visualize your fitness progress over time</p>
              </div>
              <div class="feature-card">
                <i class="fas fa-dumbbell"></i>
                <h3>Workout Logging</h3>
                <p>Record your workouts and track performance</p>
              </div>
            </div>
          </div>
          
          <div class="auth-form-container">
            <div class="auth-card">
              <div class="auth-header">
                <h2>Welcome to SporTTrackers</h2>
                <p>Sign in to your account or create a new one to get started.</p>
              </div>
              
              <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login">Login</button>
                <button class="auth-tab" data-tab="register">Register</button>
              </div>
              
              <div class="auth-form login-form active">
                <form id="loginForm">
                  <div class="form-group">
                    <label for="loginUsername">Username</label>
                    <input type="text" id="loginUsername" placeholder="Enter your username" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" placeholder="Enter your password" required>
                  </div>
                  
                  <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <div class="oauth-login">
                  <a href="/auth/google" class="btn btn-google">
                    <i class="fab fa-google"></i> Login with Google
                  </a>
                </div>
              </div>
              
              <div class="auth-form register-form">
                <form id="registerForm">
                  <div class="form-group">
                    <label for="registerUsername">Username</label>
                    <input type="text" id="registerUsername" placeholder="Choose a username" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="registerName">Full Name</label>
                    <input type="text" id="registerName" placeholder="Enter your full name">
                  </div>
                  
                  <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" placeholder="Enter your email">
                  </div>
                  
                  <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" id="registerPassword" placeholder="Create a password" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="registerConfirmPassword">Confirm Password</label>
                    <input type="password" id="registerConfirmPassword" placeholder="Confirm your password" required>
                  </div>
                  
                  <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>
              </div>
              
              <div class="auth-footer">
                <p>By continuing, you agree to our Terms of Service and Privacy Policy.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dashboard Page content -->
      <div class="page-content" id="dashboardPage">
        <div class="page-header">
          <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <span class="username"></span></p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary"><i class="fas fa-plus"></i> New Workout</button>
            <button class="btn btn-outline"><i class="fas fa-calendar"></i> View Calendar</button>
          </div>
        </div>
        
        <!-- Today's Activity Summary -->
        <section class="dashboard-section">
          <h2 class="section-title">Today's Activity</h2>
          <div class="grid-3">
            <div class="activity-card">
              <div class="activity-icon blue">
                <i class="fas fa-walking"></i>
              </div>
              <div class="activity-details">
                <h3>Steps</h3>
                <p class="activity-value">-</p>
                <div class="activity-trend">
                  <span class="trend-up"><i class="fas fa-arrow-up"></i></span>
                  <span class="trend-text"></span>
                </div>
                <div class="progress-container">
                  <div class="progress-bar"></div>
                </div>
                <p class="progress-text"></p>
              </div>
            </div>
            
            <div class="activity-card">
              <div class="activity-icon red">
                <i class="fas fa-fire"></i>
              </div>
              <div class="activity-details">
                <h3>Calories</h3>
                <p class="activity-value">-</p>
                <div class="activity-trend">
                  <span class="trend-down"><i class="fas fa-arrow-down"></i></span>
                  <span class="trend-text"></span>
                </div>
                <div class="progress-container">
                  <div class="progress-bar red"></div>
                </div>
                <p class="progress-text"></p>
              </div>
            </div>
            
            <div class="activity-card">
              <div class="activity-icon purple">
                <i class="fas fa-stopwatch"></i>
              </div>
              <div class="activity-details">
                <h3>Active Minutes</h3>
                <p class="activity-value">-</p>
                <div class="activity-trend">
                  <span class="trend-up"><i class="fas fa-arrow-up"></i></span>
                  <span class="trend-text"></span>
                </div>
                <div class="progress-container">
                  <div class="progress-bar purple"></div>
                </div>
                <p class="progress-text"></p>
              </div>
            </div>
          </div>
        </section>
        
        <!-- Weekly Activity Chart -->
        <section class="dashboard-section">
          <div class="section-header">
            <h2 class="section-title">Weekly Activity</h2>
            <div class="tab-buttons">
              <button class="tab-btn active" data-tab="activity">Activity</button>
              <button class="tab-btn" data-tab="heart-rate">Heart Rate</button>
            </div>
          </div>
          <div class="card">
            <div class="tab-content activity-chart active" data-tab-content="activity">
              <canvas id="activityChart" height="250"></canvas>
            </div>
            <div class="tab-content heart-chart" data-tab-content="heart-rate">
              <canvas id="heartRateChart" height="250"></canvas>
            </div>
          </div>
        </section>
        
        <!-- Recent Workouts -->
        <section class="dashboard-section">
          <div class="section-header">
            <h2 class="section-title">Recent Workouts</h2>
            <a href="#" class="view-all" data-page="workoutsPage">
              View All <i class="fas fa-chevron-right"></i>
            </a>
          </div>
          
          <div class="grid-2" id="dashboardRecentWorkouts">
            <!-- JS will render recent workouts here -->
          </div>
        </section>
        
        <!-- Health Metrics -->
        <section class="dashboard-section">
          <div class="section-header">
            <h2 class="section-title">Health Metrics</h2>
            <a href="#" class="view-all" data-page="healthMetricsPage">
              Track New Metric <i class="fas fa-chevron-right"></i>
            </a>
          </div>
          
          <div class="card">
            <table class="metrics-table" id="dashboardMetricsTable">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Current</th>
                  <th>Previous</th>
                  <th>Change</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <!-- Health metrics will be rendered here by JS -->
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Workouts Page content -->
      <div class="page-content" id="workoutsPage">
        <div class="page-header">
          <div>
            <h1>Workouts</h1>
            <p>Manage and track your workout activities</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary" id="newWorkoutBtn"><i class="fas fa-plus"></i> New Workout</button>
          </div>
        </div>
        
        <div class="filters-container">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search workouts...">
          </div>
          
          <div class="filter-actions">
            <div class="select-container">
              <select class="filter-select" id="workoutTypeFilter">
                <option value="">All types</option>
                <option value="running">Running</option>
                <option value="cycling">Cycling</option>
                <option value="strength">Strength Training</option>
                <option value="yoga">Yoga</option>
                <option value="swimming">Swimming</option>
              </select>
            </div>
            
            <div class="view-toggle">
              <button class="view-btn active" data-view="list"><i class="fas fa-list"></i></button>
              <button class="view-btn" data-view="grid"><i class="fas fa-th-large"></i></button>
            </div>
          </div>
        </div>
        
        <div class="workouts-list-view">
          <!-- REMOVE all static workout-list-card blocks here! -->
        </div>
        
        <!-- New Workout Modal -->
        <div class="modal" id="newWorkoutModal">
          <div class="modal-content">
            <div class="modal-header">
              <h2>Add New Workout</h2>
              <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
              <form id="workoutForm">
                <div class="form-group">
                  <label for="workoutTitle">Workout Title</label>
                  <input type="text" id="workoutTitle" placeholder="Enter workout title" required>
                </div>
                
                <div class="form-group">
                  <label for="workoutType">Workout Type</label>
                  <select id="workoutType" required>
                    <option value="">Select workout type</option>
                    <option value="running">Running</option>
                    <option value="cycling">Cycling</option>
                    <option value="strength">Strength Training</option>
                    <option value="yoga">Yoga</option>
                    <option value="swimming">Swimming</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="workoutDate">Date</label>
                    <input type="date" id="workoutDate" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="workoutTime">Time</label>
                    <input type="time" id="workoutTime" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="workoutDuration">Duration (minutes)</label>
                    <input type="number" id="workoutDuration" min="1" placeholder="0" required>
                  </div>
                  
                  <div class="form-group" id="distanceField">
                    <label for="workoutDistance">Distance (km)</label>
                    <input type="number" id="workoutDistance" min="0" step="0.1" placeholder="0">
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="workoutCalories">Calories Burned</label>
                  <input type="number" id="workoutCalories" min="0" placeholder="0">
                </div>
                
                <div class="form-group">
                  <label for="workoutNotes">Notes</label>
                  <textarea id="workoutNotes" rows="3" placeholder="Add notes about your workout"></textarea>
                </div>
                
                <div class="form-actions">
                  <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Workout</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Health Metrics Page content -->
      <div class="page-content" id="healthMetricsPage">
        <div class="page-header">
          <div>
            <h1>Health Metrics</h1>
            <p>Monitor and track your health statistics</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary" id="newMetricBtn"><i class="fas fa-plus"></i> Record New Metric</button>
          </div>
        </div>
        <div class="card">
          <table class="metrics-table">
            <thead>
              <tr>
                <th>Metric</th>
                <th>Current</th>
                <th>Previous</th>
                <th>Change</th>
                <th>Status</th>
                <th>Last Updated</th>
              </tr>
            </thead>
            <tbody>
              <!-- Health metrics will be rendered here by JS -->
            </tbody>
          </table>
        </div>
        <div class="metrics-cards-view" style="display: none;">
          <!-- Health metric cards will be rendered here by JS -->
        </div>
        <!-- New Metric Modal -->
        <div class="modal" id="newMetricModal">
          <div class="modal-content">
            <div class="modal-header">
              <h2>Record New Health Metric</h2>
              <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
              <form id="metricForm">
                <div class="form-group">
                  <label for="metricType">Metric Type</label>
                  <select id="metricType" required>
                    <option value="">Select metric type</option>
                    <option value="heart_rate">Heart Rate</option>
                    <option value="weight">Weight</option>
                    <option value="sleep">Sleep Duration</option>
                    <option value="blood_pressure">Blood Pressure</option>
                    <option value="hydration">Water Intake</option>
                  </select>
                </div>
                <div class="form-group" id="singleValueField">
                  <label for="metricValue">Value</label>
                  <input type="number" id="metricValue" step="0.1" placeholder="Enter value">
                </div>
                <div class="form-row" id="bloodPressureField" style="display: none;">
                  <div class="form-group">
                    <label for="systolicValue">Systolic (mmHg)</label>
                    <input type="number" id="systolicValue" placeholder="120">
                  </div>
                  <div class="form-group">
                    <label for="diastolicValue">Diastolic (mmHg)</label>
                    <input type="number" id="diastolicValue" placeholder="80">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label for="metricDate">Date</label>
                    <input type="date" id="metricDate" required>
                  </div>
                  <div class="form-group">
                    <label for="metricTime">Time</label>
                    <input type="time" id="metricTime" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="metricNotes">Notes</label>
                  <textarea id="metricNotes" rows="3" placeholder="Add notes about this measurement"></textarea>
                </div>
                <div class="form-actions">
                  <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Metric</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Progress Page content -->
      <div class="page-content" id="progressPage">
        <div class="page-header">
          <div>
            <h1>Progress</h1>
            <p>Track your fitness journey progress over time</p>
          </div>
          <div class="header-actions">
            <div class="select-container">
              <select id="progressPeriod">
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
                <option value="3month">Last 3 Months</option>
                <option value="year">This Year</option>
                <option value="all">All Time</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="grid-2">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Weight Trend</h3>
              <div class="card-actions">
                <button class="icon-btn"><i class="fas fa-ellipsis-v"></i></button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="weightChart" height="250"></canvas>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Heart Rate Trends</h3>
              <div class="card-actions">
                <button class="icon-btn"><i class="fas fa-ellipsis-v"></i></button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="heartRateTrendChart" height="250"></canvas>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Workout Volume</h3>
            <div class="card-actions">
              <div class="tab-buttons small">
                <button class="tab-btn active" data-tab="duration">Duration</button>
                <button class="tab-btn" data-tab="distance">Distance</button>
                <button class="tab-btn" data-tab="calories">Calories</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content active" data-tab-content="duration">
              <canvas id="workoutDurationChart" height="250"></canvas>
            </div>
            <div class="tab-content" data-tab-content="distance">
              <canvas id="workoutDistanceChart" height="250"></canvas>
            </div>
            <div class="tab-content" data-tab-content="calories">
              <canvas id="workoutCaloriesChart" height="250"></canvas>
            </div>
          </div>
        </div>
        
        <div class="grid-2">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Workout Distribution</h3>
              <div class="card-actions">
                <button class="icon-btn"><i class="fas fa-ellipsis-v"></i></button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="workoutDistributionChart" height="250"></canvas>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Activity Consistency</h3>
              <div class="card-actions">
                <button class="icon-btn"><i class="fas fa-ellipsis-v"></i></button>
              </div>
            </div>
            <div class="card-body">
              <div class="calendar-heatmap">
                <!-- Calendar heatmap will be rendered here via JavaScript -->
                <div class="placeholder-text">Calendar heatmap showing workout frequency</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Goals Progress</h3>
            <div class="card-actions">
              <button class="btn btn-sm btn-outline" id="addGoalBtn"><i class="fas fa-plus"></i> Set New Goal</button>
            </div>
          </div>
          <div class="card-body">
            <div class="goals-list">
              <!-- Goals will be rendered here by JS -->
            </div>
          </div>
        </div>
      </div>
      
      <!-- Profile Page content -->
      <div class="page-content" id="profilePage">
        <div class="page-header">
          <div>
            <h1>Profile</h1>
            <p>Manage your personal information and profile settings</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary" id="saveProfileBtn"><i class="fas fa-save"></i> Save Changes</button>
          </div>
        </div>
        
        <div class="profile-container">
          <div class="profile-sidebar">
            <div class="profile-avatar-container">
              <div class="profile-avatar">
                <span>JD</span>
              </div>
              <button class="btn btn-sm btn-outline avatar-btn"><i class="fas fa-camera"></i> Change</button>
            </div>
            
            <div class="profile-stats">
              <div class="stat-item">
                <div class="stat-label">Member Since</div>
                <div class="stat-value">March 2023</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Workouts Completed</div>
                <div class="stat-value">42</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Current Streak</div>
                <div class="stat-value">8 days</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Best Streak</div>
                <div class="stat-value">14 days</div>
              </div>
            </div>
            
            <div class="profile-nav">
              <a href="#personalInfo" class="profile-nav-item active">Personal Information</a>
              <a href="#accountSettings" class="profile-nav-item">Account Settings</a>
              <a href="#notificationPreferences" class="profile-nav-item">Notification Preferences</a>
              <a href="#privacySettings" class="profile-nav-item">Privacy Settings</a>
            </div>
          </div>
          
          <div class="profile-content">
            <div class="card" id="personalInfo">
              <div class="card-header">
                <h3 class="card-title">Personal Information</h3>
              </div>
              <div class="card-body">
                <form id="personalInfoForm">
                  <div class="form-row">
                    <div class="form-group">
                      <label for="firstName">First Name</label>
                      <input type="text" id="firstName" value="John">
                    </div>
                    
                    <div class="form-group">
                      <label for="lastName">Last Name</label>
                      <input type="text" id="lastName" value="Doe">
                    </div>
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="email">Email Address</label>
                      <input type="email" id="email" value="john.doe@example.com">
                    </div>
                    
                    <div class="form-group">
                      <label for="phone">Phone Number</label>
                      <input type="tel" id="phone" value="+1 (555) 123-4567">
                    </div>
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="birthdate">Date of Birth</label>
                      <input type="date" id="birthdate" value="1985-06-15">
                    </div>
                    
                    <div class="form-group">
                      <label for="gender">Gender</label>
                      <select id="gender">
                        <option value="male" selected>Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer-not">Prefer not to say</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="height">Height (cm)</label>
                      <input type="number" id="height" value="178">
                    </div>
                    
                    <div class="form-group">
                      <label for="weight">Weight (kg)</label>
                      <input type="number" id="weight" step="0.1" value="75.5">
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="accountSettings">
              <div class="card-header">
                <h3 class="card-title">Account Settings</h3>
              </div>
              <div class="card-body">
                <form id="accountSettingsForm">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="johndoe">
                  </div>
                  
                  <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" placeholder="Enter current password">
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="newPassword">New Password</label>
                      <input type="password" id="newPassword" placeholder="Enter new password">
                    </div>
                    
                    <div class="form-group">
                      <label for="confirmPassword">Confirm New Password</label>
                      <input type="password" id="confirmPassword" placeholder="Confirm new password">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="language">Language</label>
                    <select id="language">
                      <option value="en" selected>English</option>
                      <option value="es">Spanish</option>
                      <option value="fr">French</option>
                      <option value="de">German</option>
                      <option value="zh">Chinese</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone">
                      <option value="utc-8" selected>Pacific Time (UTC-8)</option>
                      <option value="utc-7">Mountain Time (UTC-7)</option>
                      <option value="utc-6">Central Time (UTC-6)</option>
                      <option value="utc-5">Eastern Time (UTC-5)</option>
                      <option value="utc+0">UTC/GMT</option>
                      <option value="utc+1">Central European Time (UTC+1)</option>
                    </select>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="notificationPreferences">
              <div class="card-header">
                <h3 class="card-title">Notification Preferences</h3>
              </div>
              <div class="card-body">
                <form id="notificationForm">
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-label">Email Notifications</div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-label">Push Notifications</div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-label">SMS Notifications</div>
                      <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                  
                  <div class="divider"></div>
                  
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Workout Reminders</div>
                        <div class="toggle-description">Get reminded about scheduled workouts</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Goal Achievements</div>
                        <div class="toggle-description">Get notified when you reach fitness goals</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Health Metric Alerts</div>
                        <div class="toggle-description">Receive alerts for significant health metric changes</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Weekly Reports</div>
                        <div class="toggle-description">Receive weekly summary of your activities</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">New Features & Updates</div>
                        <div class="toggle-description">Stay informed about new app features</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="privacySettings">
              <div class="card-header">
                <h3 class="card-title">Privacy Settings</h3>
              </div>
              <div class="card-body">
                <form id="privacyForm">
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Profile Visibility</div>
                        <div class="toggle-description">Make your profile visible to other users</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Share Workout Activity</div>
                        <div class="toggle-description">Allow others to see your workout activities</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Share Health Metrics</div>
                        <div class="toggle-description">Allow sharing of your health metrics data</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Data for Research</div>
                        <div class="toggle-description">Allow anonymous data to be used for research</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <button type="button" class="btn btn-outline btn-block">Download My Data</button>
                  </div>
                  
                  <div class="form-group">
                    <button type="button" class="btn btn-outline danger btn-block">Delete My Account</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Settings Page content -->
      <div class="page-content" id="settingsPage">
        <div class="page-header">
          <div>
            <h1>Settings</h1>
            <p>Configure your app preferences and settings</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary" id="saveSettingsBtn"><i class="fas fa-save"></i> Save Changes</button>
          </div>
        </div>
        
        <div class="settings-container">
          <div class="settings-sidebar">
            <div class="settings-nav">
              <a href="#generalSettings" class="settings-nav-item active">General</a>
              <a href="#displaySettings" class="settings-nav-item">Display & Theme</a>
              <a href="#unitsSettings" class="settings-nav-item">Units & Formats</a>
              <a href="#goalsSettings" class="settings-nav-item">Goals & Targets</a>
              <a href="#connectionsSettings" class="settings-nav-item">Connected Devices</a>
              <a href="#exportSettings" class="settings-nav-item">Export & Backup</a>
            </div>
          </div>
          
          <div class="settings-content">
            <div class="card" id="generalSettings">
              <div class="card-header">
                <h3 class="card-title">General Settings</h3>
              </div>
              <div class="card-body">
                <form id="generalSettingsForm">
                  <div class="form-group">
                    <label for="startDayOfWeek">Start Day of Week</label>
                    <select id="startDayOfWeek">
                      <option value="monday" selected>Monday</option>
                      <option value="sunday">Sunday</option>
                      <option value="saturday">Saturday</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="dateFormat">Date Format</label>
                    <select id="dateFormat">
                      <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                      <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                      <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="timeFormat">Time Format</label>
                    <select id="timeFormat">
                      <option value="12h" selected>12-hour (AM/PM)</option>
                      <option value="24h">24-hour</option>
                    </select>
                  </div>
                  
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Automatic Workout Detection</div>
                        <div class="toggle-description">Automatically detect and log workouts</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">GPS Tracking</div>
                        <div class="toggle-description">Track location during workouts</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Workout Auto-Pause</div>
                        <div class="toggle-description">Automatically pause tracking when you stop moving</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="displaySettings">
              <div class="card-header">
                <h3 class="card-title">Display & Theme</h3>
              </div>
              <div class="card-body">
                <form id="displaySettingsForm">
                  <div class="form-group">
                    <label>Theme</label>
                    <div class="theme-options">
                      <div class="theme-option active">
                        <div class="theme-preview light-theme"></div>
                        <div class="theme-label">Light</div>
                      </div>
                      <div class="theme-option">
                        <div class="theme-preview dark-theme"></div>
                        <div class="theme-label">Dark</div>
                      </div>
                      <div class="theme-option">
                        <div class="theme-preview system-theme"></div>
                        <div class="theme-label">System</div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Accent Color</label>
                    <div class="color-options">
                      <div class="color-option active" style="--color: #3d5aff;"></div>
                      <div class="color-option" style="--color: #ff4757;"></div>
                      <div class="color-option" style="--color: #2ed573;"></div>
                      <div class="color-option" style="--color: #ffa502;"></div>
                      <div class="color-option" style="--color: #7d5fff;"></div>
                      <div class="color-option" style="--color: #ff6b81;"></div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="fontSize">Font Size</label>
                    <select id="fontSize">
                      <option value="small">Small</option>
                      <option value="medium" selected>Medium</option>
                      <option value="large">Large</option>
                      <option value="x-large">Extra Large</option>
                    </select>
                  </div>
                  
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Animations</div>
                        <div class="toggle-description">Enable UI animations and transitions</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Compact Mode</div>
                        <div class="toggle-description">Use a more compact layout</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="unitsSettings">
              <div class="card-header">
                <h3 class="card-title">Units & Formats</h3>
              </div>
              <div class="card-body">
                <form id="unitsSettingsForm">
                  <div class="form-group">
                    <label for="distanceUnit">Distance Unit</label>
                    <select id="distanceUnit">
                      <option value="km" selected>Kilometers (km)</option>
                      <option value="miles">Miles (mi)</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="weightUnit">Weight Unit</label>
                    <select id="weightUnit">
                      <option value="kg" selected>Kilograms (kg)</option>
                      <option value="lbs">Pounds (lbs)</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="heightUnit">Height Unit</label>
                    <select id="heightUnit">
                      <option value="cm" selected>Centimeters (cm)</option>
                      <option value="feet">Feet and inches (ft/in)</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="energyUnit">Energy Unit</label>
                    <select id="energyUnit">
                      <option value="kcal" selected>Kilocalories (kcal)</option>
                      <option value="kj">Kilojoules (kJ)</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="paceUnit">Pace Display</label>
                    <select id="paceUnit">
                      <option value="min_km" selected>min/km</option>
                      <option value="min_mile">min/mile</option>
                      <option value="kph">km/h</option>
                      <option value="mph">mph</option>
                    </select>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="goalsSettings">
              <div class="card-header">
                <h3 class="card-title">Goals & Targets</h3>
              </div>
              <div class="card-body">
                <form id="goalsSettingsForm">
                  <div class="setting-section">
                    <h4 class="setting-section-title">Daily Activity Targets</h4>
                    
                    <div class="form-row">
                      <div class="form-group">
                        <label for="stepsGoal">Daily Steps</label>
                        <input type="number" id="stepsGoal" value="10000">
                      </div>
                      
                      <div class="form-group">
                        <label for="caloriesGoal">Daily Calories Burned</label>
                        <input type="number" id="caloriesGoal" value="500">
                      </div>
                    </div>
                    
                    <div class="form-row">
                      <div class="form-group">
                        <label for="activeMinutesGoal">Active Minutes</label>
                        <input type="number" id="activeMinutesGoal" value="60">
                      </div>
                      
                      <div class="form-group">
                        <label for="waterGoal">Water Intake (ml)</label>
                        <input type="number" id="waterGoal" value="2500">
                      </div>
                    </div>
                  </div>
                  
                  <div class="setting-section">
                    <h4 class="setting-section-title">Workout Targets</h4>
                    
                    <div class="form-row">
                      <div class="form-group">
                        <label for="workoutsPerWeekGoal">Workouts Per Week</label>
                        <input type="number" id="workoutsPerWeekGoal" value="4">
                      </div>
                      
                      <div class="form-group">
                        <label for="workoutDurationGoal">Workout Duration (min)</label>
                        <input type="number" id="workoutDurationGoal" value="45">
                      </div>
                    </div>
                  </div>
                  
                  <div class="setting-section">
                    <h4 class="setting-section-title">Health Targets</h4>
                    
                    <div class="form-row">
                      <div class="form-group">
                        <label for="weightGoal">Target Weight (kg)</label>
                        <input type="number" id="weightGoal" step="0.1" value="72.0">
                      </div>
                      
                      <div class="form-group">
                        <label for="restingHrGoal">Target Resting Heart Rate (bpm)</label>
                        <input type="number" id="restingHrGoal" value="65">
                      </div>
                    </div>
                    
                    <div class="form-row">
                      <div class="form-group">
                        <label for="sleepGoal">Sleep Duration (hours)</label>
                        <input type="number" id="sleepGoal" step="0.5" value="8">
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="card" id="connectionsSettings">
              <div class="card-header">
                <h3 class="card-title">Connected Devices & Apps</h3>
              </div>
              <div class="card-body">
                <div class="connections-list">
                  <div class="connection-item">
                    <div class="connection-info">
                      <div class="connection-icon">
                        <i class="fas fa-heartbeat"></i>
                      </div>
                      <div>
                        <h4>FitBit Sense</h4>
                        <p>Connected on May 1, 2023</p>
                      </div>
                    </div>
                    <div class="connection-actions">
                      <button class="btn btn-sm btn-outline">Disconnect</button>
                    </div>
                  </div>
                  
                  <div class="connection-item">
                    <div class="connection-info">
                      <div class="connection-icon">
                        <i class="fas fa-running"></i>
                      </div>
                      <div>
                        <h4>Strava</h4>
                        <p>Connected on April 15, 2023</p>
                      </div>
                    </div>
                    <div class="connection-actions">
                      <button class="btn btn-sm btn-outline">Disconnect</button>
                    </div>
                  </div>
                </div>
                
                <div class="divider"></div>
                
                <h4>Connect New Device or App</h4>
                <div class="connect-options">
                  <button class="connect-option">
                    <div class="connection-icon">
                      <i class="fas fa-heart"></i>
                    </div>
                    <span>Apple Health</span>
                  </button>
                  
                  <button class="connect-option">
                    <div class="connection-icon">
                      <i class="fas fa-mobile-alt"></i>
                    </div>
                    <span>Garmin</span>
                  </button>
                  
                  <button class="connect-option">
                    <div class="connection-icon">
                      <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span>Google Fit</span>
                  </button>
                  
                  <button class="connect-option">
                    <div class="connection-icon">
                      <i class="fas fa-stopwatch"></i>
                    </div>
                    <span>Samsung Health</span>
                  </button>
                </div>
              </div>
            </div>
            
            <div class="card" id="exportSettings">
              <div class="card-header">
                <h3 class="card-title">Export & Backup</h3>
              </div>
              <div class="card-body">
                <div class="setting-section">
                  <h4 class="setting-section-title">Export Data</h4>
                  <p class="setting-description">Export your fitness data in various formats for backup or use in other applications.</p>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="exportFormat">Export Format</label>
                      <select id="exportFormat">
                        <option value="csv">CSV (Spreadsheet)</option>
                        <option value="json">JSON</option>
                        <option value="xml">XML</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="exportDateRange">Date Range</label>
                      <select id="exportDateRange">
                        <option value="all">All Data</option>
                        <option value="last-month">Last Month</option>
                        <option value="last-3months">Last 3 Months</option>
                        <option value="last-year">Last Year</option>
                        <option value="custom">Custom Range</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Data to Export</label>
                    <div class="checkbox-group">
                      <label class="checkbox-label">
                        <input type="checkbox" checked> Workouts
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" checked> Health Metrics
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" checked> Daily Activity
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox"> Profile Information
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox"> Goals and Targets
                      </label>
                    </div>
                  </div>
                  
                  <div class="form-actions">
                    <button type="button" class="btn btn-primary">Export Data</button>
                  </div>
                </div>
                
                <div class="divider"></div>
                
                <div class="setting-section">
                  <h4 class="setting-section-title">Automatic Backups</h4>
                  <p class="setting-description">Configure automatic backups of your fitness data.</p>
                  
                  <div class="toggle-group">
                    <div class="toggle-item">
                      <div class="toggle-info">
                        <div class="toggle-label">Enable Automatic Backups</div>
                        <div class="toggle-description">Regularly backup your data to cloud storage</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="backupFrequency">Backup Frequency</label>
                    <select id="backupFrequency">
                      <option value="daily">Daily</option>
                      <option value="weekly" selected>Weekly</option>
                      <option value="monthly">Monthly</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="backupLocation">Backup Location</label>
                    <select id="backupLocation">
                      <option value="cloud">Cloud Storage (Default)</option>
                      <option value="google-drive">Google Drive</option>
                      <option value="dropbox">Dropbox</option>
                      <option value="onedrive">OneDrive</option>
                    </select>
                  </div>
                  
                  <div class="form-actions">
                    <button type="button" class="btn btn-outline">Backup Now</button>
                    <button type="button" class="btn btn-outline">Restore from Backup</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  
  <script src="/js/script.js"></script>

  <!-- Add this modal at the end of your main content -->
  <div class="modal" id="addGoalModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add New Goal</h2>
        <button class="close-modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="addGoalForm">
          <div class="form-group">
            <label for="goalType">Goal Type</label>
            <select id="goalType" required>
              <option value="">Select goal type</option>
              <option value="workout">Workout Count</option>
              <option value="weight">Weight</option>
              <option value="heart_rate">Resting Heart Rate</option>
              <option value="custom">Custom</option>
            </select>
          </div>
          <div class="form-group">
            <label for="goalDescription">Description</label>
            <input type="text" id="goalDescription" placeholder="e.g. Run 5km in under 25 minutes" required>
          </div>
          <div class="form-group">
            <label for="goalTarget">Target Value</label>
            <input type="number" id="goalTarget" step="any" placeholder="e.g. 5 or 72">
          </div>
          <div class="form-group">
            <label for="goalUnit">Unit</label>
            <input type="text" id="goalUnit" placeholder="e.g. km, kg, bpm">
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Goal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>