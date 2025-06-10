// Change this to match your setup
const BASE_URL = '/api-uas-sporttracker/public';

document.addEventListener('DOMContentLoaded', async function() {
  // Initialize content first
  initContent();

  // Check login status
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  let valid = false;
  let username = localStorage.getItem('username') || '';

  if (isLoggedIn && localStorage.getItem('jwtToken')) {
    // Validate token and get user info
    try {
      const res = await fetch(`${BASE_URL}/api/me.php`, {
        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('jwtToken') }
      });
      const data = await res.json();
      if (res.ok && data.success) {
        valid = true;
        // Optionally fetch user info from DB here
        if (data.user && data.user.name) {
          username = data.user.name || data.user.username;
          localStorage.setItem('username', username);
        } else if (data.username) {
          username = data.username;
          localStorage.setItem('username', username);
        }
        updateUsernameUI(username);
      }
    } catch (e) {
      valid = false;
    }
  }

  if (!isLoggedIn || !valid) {
    localStorage.removeItem('jwtToken');
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('username');
    showPage('authPage');
    hideElements(['.sidebar', '.mobile-header', '.mobile-bottom-nav']);
  } else {
    showPage('dashboardPage');
    updateUsernameUI(username);
  }

  // Initialize all event listeners
  initNavigationEvents();
  initAuthEvents();
  initModals();
  initTabEvents();
  initFilterEvents();
  initCharts();
  fetchAndRenderWorkouts();

  const filterSelect = document.getElementById('workoutTypeFilter');
  if (filterSelect) {
    filterSelect.addEventListener('change', function() {
      filterWorkoutsByType(this.value);
    });
  }
});

// (Removed erroneous duplicate initContent function)

// Make sure all page content divs exist and are properly structured
function initContent() {
  const pages = ['dashboardPage', 'workoutsPage', 'healthMetricsPage', 'progressPage', 'profilePage', 'settingsPage', 'authPage'];
  
  // Check if pages exist
  pages.forEach(pageId => {
    const page = document.getElementById(pageId);
    
    // If page doesn't exist, print error
    if (!page) {
      console.error(`Page element with ID "${pageId}" not found!`);
    } else {
      // Make sure page has the page-content class
      if (!page.classList.contains('page-content')) {
        page.classList.add('page-content');
      }
    }
  });
}

// Navigation between pages
function initNavigationEvents() {
  console.log('Initializing navigation events');
  
  // Sidebar navigation
  document.querySelectorAll('.nav-item, .bottom-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const targetPage = this.getAttribute('data-page');
      console.log('Nav clicked:', targetPage);
      navigateToPage(targetPage);
    });
  });
  
  // "View all" links
  document.querySelectorAll('.view-all').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetPage = this.getAttribute('data-page');
      navigateToPage(targetPage);
    });
  });
  
  // Logout buttons
  document.querySelectorAll('.logout-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      logout();
    });
  });
  
  // Mobile menu toggle
  const menuToggle = document.getElementById('mobileMenuToggle');
  const mobileDrawer = document.getElementById('mobileDrawer');
  const closeMenu = document.getElementById('closeMenu');
  
  if (menuToggle && mobileDrawer && closeMenu) {
    menuToggle.addEventListener('click', function() {
      mobileDrawer.classList.add('open');
      // Create overlay
      const overlay = document.createElement('div');
      overlay.className = 'drawer-overlay visible';
      document.body.appendChild(overlay);
      
      overlay.addEventListener('click', closeDrawer);
    });
    
    closeMenu.addEventListener('click', closeDrawer);
  }
  
  function closeDrawer() {
    mobileDrawer.classList.remove('open');
    const overlay = document.querySelector('.drawer-overlay');
    if (overlay) {
      overlay.classList.remove('visible');
      setTimeout(() => {
        overlay.remove();
      }, 300);
    }
  }
  
  // Profile page navigation
  document.querySelectorAll('.profile-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const targetSection = this.getAttribute('href').substring(1);
      
      // Update active state
      document.querySelectorAll('.profile-nav-item').forEach(navItem => {
        navItem.classList.remove('active');
      });
      this.classList.add('active');
      
      // Scroll to section
      const section = document.getElementById(targetSection);
      if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
  
  // Settings page navigation
  document.querySelectorAll('.settings-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const targetSection = this.getAttribute('href').substring(1);
      
      // Update active state
      document.querySelectorAll('.settings-nav-item').forEach(navItem => {
        navItem.classList.remove('active');
      });
      this.classList.add('active');
      
      // Scroll to section
      const section = document.getElementById(targetSection);
      if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
}

// Navigate to a specific page
function navigateToPage(targetPage) {
  console.log('Navigating to:', targetPage);
  
  // Make sure the target page exists
  const targetElement = document.getElementById(targetPage);
  if (!targetElement) {
    console.error(`Target page element with ID "${targetPage}" not found!`);
    return;
  }
  
  // Hide all pages
  document.querySelectorAll('.page-content').forEach(page => {
    page.classList.remove('active');
  });
  
  // Show target page
  targetElement.classList.add('active');
  
  // Update navigation active states
  document.querySelectorAll('.nav-item, .bottom-nav-item').forEach(item => {
    if (item.getAttribute('data-page') === targetPage) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
  
  // Scroll back to top
  window.scrollTo(0, 0);
  
  // Close mobile drawer if open
  const mobileDrawer = document.getElementById('mobileDrawer');
  if (mobileDrawer && mobileDrawer.classList.contains('open')) {
    mobileDrawer.classList.remove('open');
    const overlay = document.querySelector('.drawer-overlay');
    if (overlay) {
      overlay.remove();
    }
  }

  // After showing the page:
  if (targetPage === 'healthMetricsPage') {
    fetchAndRenderMetrics();
  }
  if (targetPage === 'progressPage') {
    initProgressCharts();
  }
  if (targetPage === 'dashboardPage') {
    fetchAndRenderTodayActivity();
    fetchAndRenderDashboardMetrics(); // <-- Add this line
  }
}

// Authentication related functions
function initAuthEvents() {
  // Auth tabs switching
  const authTabs = document.querySelectorAll('.auth-tab');
  if (authTabs.length) {
    authTabs.forEach(tab => {
      tab.addEventListener('click', function() {
        const targetTab = this.getAttribute('data-tab');
        
        // Update tab active state
        document.querySelectorAll('.auth-tab').forEach(t => {
          t.classList.remove('active');
        });
        this.classList.add('active');
        
        // Show corresponding form
        document.querySelectorAll('.auth-form').forEach(form => {
          form.classList.remove('active');
        });
        document.querySelector(`.${targetTab}-form`).classList.add('active');
      });
    });
  }
  
  // Login form submission
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const username = document.getElementById('loginUsername').value;
      const password = document.getElementById('loginPassword').value;
      
      // Simple validation
      if (!username || !password) {
        alert('Please enter both username and password');
        return;
      }
      
      // Mock login - in a real app, this would be an API call
      login(username, password);
    });
  }
  
  // Registration form submission
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const username = document.getElementById('registerUsername').value;
      const name = document.getElementById('registerName').value;
      const email = document.getElementById('registerEmail').value;
      const password = document.getElementById('registerPassword').value;
      const confirmPassword = document.getElementById('registerConfirmPassword').value;
      
      // Simple validation
      if (!username || !password) {
        alert('Username and password are required');
        return;
      }
      
      if (password !== confirmPassword) {
        alert('Passwords do not match');
        return;
      }
      
      // Mock registration - in a real app, this would be an API call
      register(username, name, email, password);
    });
  }
}

// Replace the mock login function
async function login(username, password) {
  try {
    const response = await fetch(`${BASE_URL}/api/login.php`, { // <-- updated endpoint
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });
    const data = await response.json();
    if (response.ok && data.token) {
      localStorage.setItem('jwtToken', data.token);
      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('username', username);
      showPage('dashboardPage');
      showElements(['.sidebar', '.mobile-header', '.mobile-bottom-nav']);
      updateUsernameUI(username);
    } else {
      alert(data.error || 'Login failed');
    }
  } catch (err) {
    alert('Login error: ' + err);
  }
}

// Replace the mock register function
async function register(username, name, email, password) {
  try {
    const response = await fetch(`${BASE_URL}/api/register.php`, { // <-- updated endpoint
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, name, email, password })
    });
    const data = await response.json();
    if (response.ok && data.token) {
      localStorage.setItem('jwtToken', data.token);
      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('username', username);
      showPage('dashboardPage');
      showElements(['.sidebar', '.mobile-header', '.mobile-bottom-nav']);
      updateUsernameUI(name || username);
    } else {
      // Show detailed error from backend
      alert(JSON.stringify(data));
    }
  } catch (err) {
    alert('Registration error');
  }
}

// Logout function
function logout() {
  // In a real app, this would also call a logout API endpoint
  localStorage.removeItem('isLoggedIn');
  localStorage.removeItem('username');
  
  // Show auth page
  showPage('authPage');
  hideElements(['.sidebar', '.mobile-header', '.mobile-bottom-nav']);
}

// Modal handling
function initModals() {
  // New Workout modal
  const newWorkoutBtn = document.getElementById('newWorkoutBtn');
  const newWorkoutModal = document.getElementById('newWorkoutModal');
  
  if (newWorkoutBtn && newWorkoutModal) {
    newWorkoutBtn.addEventListener('click', function() {
      openModal(newWorkoutModal);
    });
  }
  
  // New Metric modal
  const newMetricBtn = document.getElementById('newMetricBtn');
  const newMetricModal = document.getElementById('newMetricModal');
  if (newMetricBtn && newMetricModal) {
    newMetricBtn.addEventListener('click', function() {
      openModal(newMetricModal);
    });
  }
  
  // Close modal buttons
  document.querySelectorAll('.close-modal, .modal-cancel').forEach(btn => {
    btn.addEventListener('click', function() {
      const modal = this.closest('.modal');
      closeModal(modal);
    });
  });
  
  // Close modal when clicking outside
  document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal(this);
      }
    });
  });
  
  // Metric type change handler
  const metricTypeSelect = document.getElementById('metricType');
  const singleValueField = document.getElementById('singleValueField');
  const bloodPressureField = document.getElementById('bloodPressureField');
  
  if (metricTypeSelect && singleValueField && bloodPressureField) {
    metricTypeSelect.addEventListener('change', function() {
      if (this.value === 'blood_pressure') {
        singleValueField.style.display = 'none';
        bloodPressureField.style.display = 'flex';
      } else {
        singleValueField.style.display = 'block';
        bloodPressureField.style.display = 'none';
      }
    });
  }
  
  // Form submissions
  const workoutForm = document.getElementById('workoutForm');
  if (workoutForm) {
    workoutForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Collect form data
        const formData = {
            title: document.getElementById('workoutTitle').value,
            type: document.getElementById('workoutType').value,
            date: document.getElementById('workoutDate').value,
            time: document.getElementById('workoutTime').value,
            duration: parseInt(document.getElementById('workoutDuration').value, 10),
            distance: parseFloat(document.getElementById('workoutDistance').value) || null,
            calories: parseInt(document.getElementById('workoutCalories').value, 10) || null,
            notes: document.getElementById('workoutNotes').value || null,
        };

        try {
            const token = localStorage.getItem('jwtToken');
            const response = await fetch(`${BASE_URL}/api/add_workout.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                closeModal(document.getElementById('newWorkoutModal'));
                fetchAndRenderWorkouts(); // <-- Make sure this is called!
            } else {
                alert(result.error || 'Failed to add workout');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding the workout');
        }
    });
  }
  
  const metricForm = document.getElementById('metricForm');
  if (metricForm) {
    metricForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const type = document.getElementById('metricType').value;
      const value = document.getElementById('metricValue').value;
      const systolic = document.getElementById('systolicValue').value;
      const diastolic = document.getElementById('diastolicValue').value;
      const date = document.getElementById('metricDate').value;
      const time = document.getElementById('metricTime').value;
      const notes = document.getElementById('metricNotes').value;

      // Prepare data for API
      let metricData = {
        type,
        date,
        time,
        notes
      };
      if (type === 'blood_pressure') {
        metricData.systolic = systolic;
        metricData.diastolic = diastolic;
      } else {
        metricData.value = value;
      }

      const token = localStorage.getItem('jwtToken');
      const response = await fetch(`${BASE_URL}/api/add_metric.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(metricData)
      });
      const result = await response.json();
      if (result.success) {
        alert('Metric recorded!');
        fetchAndRenderMetrics(); // Refresh metrics table
      } else {
        alert(result.error || 'Failed to record metric');
      }
      closeModal(document.getElementById('newMetricModal'));
    });
  }

  // Open modal on button click
  const addGoalBtn = document.getElementById('addGoalBtn');
  const addGoalModal = document.getElementById('addGoalModal');
  if (addGoalBtn && addGoalModal) {
    addGoalBtn.addEventListener('click', function() {
      openModal(addGoalModal);
    });
  }

  // Handle add goal form submission
  const addGoalForm = document.getElementById('addGoalForm');
  if (addGoalForm) {
    addGoalForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const type = document.getElementById('goalType').value;
      const description = document.getElementById('goalDescription').value;
      const target_value = document.getElementById('goalTarget').value;
      const unit = document.getElementById('goalUnit').value;

      const token = localStorage.getItem('jwtToken');
      const response = await fetch(`${BASE_URL}/api/add_goal.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
          type,
          description,
          target_value,
          unit
        })
      });
      const result = await response.json();
      if (result.success) {
        alert('Goal added!');
        await fetch(`${BASE_URL}/api/update_goal_progress.php`, {
          headers: { 'Authorization': 'Bearer ' + token }
        });
        fetchAndRenderGoals();
        closeModal(addGoalModal);
      } else {
        alert(result.error || 'Failed to add goal');
      }
    });
  }
}

function openModal(modal) {
  if (modal) {
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modal) {
  if (modal) {
    modal.classList.remove('open');
    document.body.style.overflow = '';
  }
}

// Tab switching
function initTabEvents() {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const tabGroup = this.closest('.tab-buttons');
      const tabContainer = this.closest('.section-header, .card-header')?.nextElementSibling;
      const targetTab = this.getAttribute('data-tab');
      
      // Update tab button active state
      tabGroup.querySelectorAll('.tab-btn').forEach(tabBtn => {
        tabBtn.classList.remove('active');
      });
      this.classList.add('active');
      
      // Show corresponding tab content
      if (tabContainer) {
        tabContainer.querySelectorAll('.tab-content').forEach(content => {
          content.classList.remove('active');
        });
        
        const targetContent = tabContainer.querySelector(`[data-tab-content="${targetTab}"]`);
        if (targetContent) {
          targetContent.classList.add('active');
        }
      }
    });
  });
}

// Filter view toggles
function initFilterEvents() {
  // Metrics view toggle
  const viewButtons = document.querySelectorAll('.view-btn');
  viewButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const viewType = this.getAttribute('data-view');
      const container = this.closest('.filters-container');
      
      // Update button active state
      container.querySelectorAll('.view-btn').forEach(viewBtn => {
        viewBtn.classList.remove('active');
      });
      this.classList.add('active');
      
      // Toggle view in health metrics page
      if (viewType === 'table') {
        const tableCard = document.querySelector('.metrics-table')?.closest('.card');
        const cardsView = document.querySelector('.metrics-cards-view');
        
        if (tableCard) tableCard.style.display = 'block';
        if (cardsView) cardsView.style.display = 'none';
      } else if (viewType === 'cards') {
        const tableCard = document.querySelector('.metrics-table')?.closest('.card');
        const cardsView = document.querySelector('.metrics-cards-view');
        
        if (tableCard) tableCard.style.display = 'none';
        if (cardsView) cardsView.style.display = 'block';
      }
    });
  });
}

// Initialize charts - only if Chart.js is loaded
function initCharts() {
  // Check if Chart is available
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js not loaded. Charts will not be initialized.');
    return;
  }
  
  try {
    // Weekly Activity Chart
    initActivityChart();
    
    // Heart Rate Chart
    initHeartRateChart();
    
    // Initialize Progress page charts
    initProgressCharts();
    
    // Initialize metric charts in card view
    initMetricCharts();
  } catch (error) {
    console.error('Error initializing charts:', error);
  }
}

function initActivityChart() {
  const activityCtx = document.getElementById('activityChart');
  if (activityCtx) {
    new Chart(activityCtx, {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [
          {
            label: 'Steps',
            data: [8421, 9234, 7562, 10125, 8745, 12350, 9876],
            backgroundColor: '#3d5aff',
            borderRadius: 4
          },
          {
            label: 'Calories',
            data: [320, 450, 280, 520, 380, 650, 480],
            backgroundColor: '#ff4757',
            borderRadius: 4
          },
          {
            label: 'Active Minutes',
            data: [45, 60, 35, 75, 50, 90, 65],
            backgroundColor: '#7d5fff',
            borderRadius: 4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { color: '#fff' }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: '#fff' }
          },
          x: {
            ticks: { color: '#fff' }
          }
        }
      }
    });
  }
}

function initHeartRateChart() {
  const heartRateCtx = document.getElementById('heartRateChart');
  if (heartRateCtx) {
    new Chart(heartRateCtx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [
          {
            label: 'Resting Heart Rate',
            data: [72, 70, 68, 69, 67, 68, 65],
            borderColor: '#ff4757',
            backgroundColor: 'rgba(255, 71, 87, 0.1)',
            fill: true,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { color: '#fff' }
          },
          tooltip: { bodyColor: '#fff', titleColor: '#fff' }
        },
        scales: {
          y: {
            beginAtZero: false,
            suggestedMin: 60,
            suggestedMax: 80,
            ticks: { color: '#fff' }
          },
          x: {
            ticks: { color: '#fff' }
          }
        }
      }
    });
  }
}

async function initProgressCharts() {
  // Fetch health metric trends
  const token = localStorage.getItem('jwtToken');
  const metricRes = await fetch(`${BASE_URL}/api/get_metric_trends.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const metricData = await metricRes.json();

  // Weight Trend Chart
  const weightCtx = document.getElementById('weightChart');
  if (weightCtx && metricData.success) {
    const labels = metricData.weight.map(w => w.date);
    const data = metricData.weight.map(w => parseFloat(w.value));
    new Chart(weightCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Weight (kg)',
          data,
          borderColor: '#3d5aff',
          backgroundColor: 'rgba(61, 90, 255, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: '#fff' } },
          tooltip: { bodyColor: '#fff', titleColor: '#fff' }
        },
        scales: {
          x: { ticks: { color: '#fff' } },
          y: { ticks: { color: '#fff' } }
        }
      }
    });
  }

  // Heart Rate Trend Chart
  const heartRateTrendCtx = document.getElementById('heartRateTrendChart');
  if (heartRateTrendCtx && metricData.success) {
    const labels = metricData.heart_rate.map(h => h.date);
    const data = metricData.heart_rate.map(h => parseFloat(h.value));
    new Chart(heartRateTrendCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Resting Heart Rate (bpm)',
          data,
          borderColor: '#ff4757',
          backgroundColor: 'rgba(255, 71, 87, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: '#fff' } },
          tooltip: { bodyColor: '#fff', titleColor: '#fff' }
        },
        scales: {
          x: { ticks: { color: '#fff' } },
          y: { ticks: { color: '#fff' } }
        }
      }
    });
  }

  // Fetch workout stats
  const workoutRes = await fetch(`${BASE_URL}/api/get_workout_stats.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const workoutData = await workoutRes.json();

  // Workout Volume Charts
  const weeks = Object.keys(workoutData.volume);
  const durationData = weeks.map(w => workoutData.volume[w].duration);
  const distanceData = weeks.map(w => workoutData.volume[w].distance);
  const caloriesData = weeks.map(w => workoutData.volume[w].calories);

  const workoutDurationCtx = document.getElementById('workoutDurationChart');
  if (workoutDurationCtx) {
    new Chart(workoutDurationCtx, {
      type: 'bar',
      data: {
        labels: weeks,
        datasets: [{
          label: 'Duration (minutes)',
          data: durationData,
          backgroundColor: '#3d5aff',
          borderRadius: 4,
          barThickness: 24,      // Fixed bar width (in px)
          maxBarThickness: 38,   // Maximum bar width (in px)
          minBarLength: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            barPercentage: 1.0,      // Use full category width
            categoryPercentage: 1.0,  // Use full category width
            ticks: { color: '#fff' }
          },
          y: {
            ticks: { color: '#fff' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#fff' }
          }
        }
      }
    });
  }
  const workoutDistanceCtx = document.getElementById('workoutDistanceChart');
  if (workoutDistanceCtx) {
    new Chart(workoutDistanceCtx, {
      type: 'bar',
      data: {
        labels: weeks,
        datasets: [{
          label: 'Distance (km)',
          data: distanceData,
          backgroundColor: '#2ed573',
          borderRadius: 4,
          barThickness: 24,
          maxBarThickness: 38,
          minBarLength: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { color: '#fff' }
          },
          y: {
            ticks: { color: '#fff' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#fff' }
          }
        }
      }
    });
  }
  const workoutCaloriesCtx = document.getElementById('workoutCaloriesChart');
  if (workoutCaloriesCtx) {
    new Chart(workoutCaloriesCtx, {
      type: 'bar',
      data: {
        labels: weeks,
        datasets: [{
          label: 'Calories Burned',
          data: caloriesData,
          backgroundColor: '#ff4757',
          borderRadius: 4,
          barThickness: 24,
          maxBarThickness: 38,
          minBarLength: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { color: '#fff' }
          },
          y: {
            ticks: { color: '#fff' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#fff' }
          }
        }
      }
    });
  }

  // Workout Distribution Chart
  const workoutDistributionCtx = document.getElementById('workoutDistributionChart');
  if (workoutDistributionCtx) {
    const types = ['running', 'cycling', 'strength', 'yoga', 'swimming'];
    const colors = ['#ff6b81', '#70a1ff', '#5f27cd', '#2ed573', '#1e90ff'];
    const distData = types.map(t => workoutData.distribution[t] || 0);
    new Chart(workoutDistributionCtx, {
      type: 'doughnut',
      data: {
        labels: ['Running', 'Cycling', 'Strength', 'Yoga', 'Swimming'],
        datasets: [{
          data: distData,
          backgroundColor: colors,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'right', labels: { color: '#fff' } } },
        cutout: '70%'
      }
    });
  }

  // Activity Consistency (Calendar Heatmap)
  // You can use a library or render a simple grid:
  const heatmapContainer = document.querySelector('.calendar-heatmap');
  if (heatmapContainer && workoutData.consistency) {
    heatmapContainer.innerHTML = '';
    const today = new Date();
    // Find the last Sunday
    const lastSunday = new Date(today);
    lastSunday.setDate(today.getDate() - today.getDay());
    // Render 5 weeks (35 days)
    const days = [];
    for (let i = 34; i >= 0; i--) {
      const d = new Date(lastSunday);
      d.setDate(lastSunday.getDate() - i);
      days.push(d);
    }
    // Create grid
    const grid = document.createElement('div');
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(7, 35px)'; // was 16px, now 28px
    grid.style.gridTemplateRows = 'repeat(5, 35px)';    // was 16px, now 28px
    grid.style.gap = '4px'; // was 2px, now 4px

    days.forEach(d => {
      const dateStr = d.toISOString().split('T')[0];
      const count = workoutData.consistency[dateStr] || 0;
      const color = count === 0 ? '#eee' : count >= 3 ? '#3d5aff' : count === 2 ? '#70a1ff' : '#b0c4ff';
      const cell = document.createElement('div');
      cell.className = 'heatmap-cell';
      cell.title = `${dateStr}: ${count} workout(s)`;
      cell.style.background = color;
      cell.style.width = '35px';   // was 16px
      cell.style.height = '35px';  // was 16px
      grid.appendChild(cell);
    });
    heatmapContainer.appendChild(grid);
  }
}

function initMetricCharts() {
  // Mini charts for metric cards
  document.querySelectorAll('.metric-chart').forEach((chartCanvas, index) => {
    let data;
    let color;
    
    // Different data and colors for each chart
    switch(index) {
      case 0: // Heart Rate
        data = [72, 71, 70, 69, 68, 70, 68];
        color = '#ff4757';
        break;
      case 1: // Weight
        data = [77.2, 76.8, 76.5, 76.0, 75.8, 75.6, 75.5];
        color = '#3d5aff';
        break;
      case 2: // Sleep
        data = [6.8, 7.0, 6.5, 6.2, 5.8, 6.0, 5.5];
        color = '#7d5fff';
        break;
      case 3: // Water Intake
        data = [1.8, 1.9, 2.0, 1.7, 1.9, 2.0, 2.1];
        color = '#2ed573';
        break;
      case 4: // Blood Pressure
        data = [122, 120, 119, 121, 118, 120, 118];
        color = '#ffa502';
        break;
      default:
        data = [70, 72, 68, 71, 69, 70, 68];
        color = '#3d5aff';
    }
    
    new Chart(chartCanvas, {
      type: 'line',
      data: {
        labels: ['', '', '', '', '', '', ''],
        datasets: [
          {
            data: data,
            borderColor: color,
            backgroundColor: color + '20',
            fill: true,
            tension: 0.4,
            pointRadius: 0
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            enabled: false
          }
        },
        scales: {
          x: {
            display: false
          },
          y: {
            display: false
          }
        }
      }
    });
  });
}

// Fetch and render workouts
async function fetchAndRenderWorkouts() {
    try {
        const token = localStorage.getItem('jwtToken');
        const response = await fetch(`${BASE_URL}/api/get_workouts.php`, {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        const result = await response.json();

        if (response.ok) {
            const workoutsContainer = document.querySelector('.workouts-list-view');
            workoutsContainer.innerHTML = ''; // Clear existing workouts

            result.workouts.forEach(workout => {
                const workoutCard = `
                    <div class="workout-list-card" data-id="${workout.id}">
                        <div class="workout-list-content">
                            <div class="workout-icon ${workout.type}">
                                <i class="fas fa-${getWorkoutIcon(workout.type)}"></i>
                            </div>
                            <div class="workout-info">
                                <div class="workout-header">
                                    <div>
                                        <h3>${workout.title}</h3>
                                        <p class="workout-date">${workout.date} ${workout.time}</p>
                                    </div>
                                    <div class="workout-actions">
                                        <button class="icon-btn delete-workout-btn"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <p class="workout-description">${workout.notes || 'No notes provided'}</p>
                                <div class="workout-stats">
                                    <div class="stat">
                                        <p class="stat-label"><i class="fas fa-clock"></i> Duration</p>
                                        <p class="stat-value">${workout.duration} min</p>
                                    </div>
                                    ${workout.distance ? `
                                    <div class="stat">
                                        <p class="stat-label"><i class="fas fa-route"></i> Distance</p>
                                        <p class="stat-value">${workout.distance} km</p>
                                    </div>` : ''}
                                    ${workout.calories ? `
                                    <div class="stat">
                                        <p class="stat-label"><i class="fas fa-fire"></i> Calories</p>
                                        <p class="stat-value">${workout.calories} kcal</p>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                workoutsContainer.insertAdjacentHTML('beforeend', workoutCard);
            });

            // Attach delete event listeners
            document.querySelectorAll('.delete-workout-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const workoutCard = this.closest('.workout-list-card');
                    const workoutId = workoutCard.getAttribute('data-id');
                    await deleteWorkout(workoutId);
                });
            });

            // Apply filter after rendering
            const filterSelect = document.getElementById('workoutTypeFilter');
            if (filterSelect) {
                filterWorkoutsByType(filterSelect.value);
            }
        } else {
            alert(result.error || 'Failed to fetch workouts');
        }
    } catch (error) {
        console.error('Error fetching workouts:', error);
    }
}

// Delete a workout
async function deleteWorkout(id) {
    try {
        const token = localStorage.getItem('jwtToken');
        const response = await fetch(`${BASE_URL}/api/delete_workout.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ id }),
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            fetchAndRenderWorkouts(); // Refresh the workouts list
        } else {
            alert(result.error || 'Failed to delete workout');
        }
    } catch (error) {
        console.error('Error deleting workout:', error);
    }
}

// Get workout icon based on type
function getWorkoutIcon(type) {
    switch (type) {
        case 'running': return 'running';
        case 'cycling': return 'biking';
        case 'strength': return 'dumbbell';
        case 'yoga': return 'spa';
        case 'swimming': return 'swimmer';
        default: return 'question-circle';
    }
}

// Add this function after fetchAndRenderWorkouts()
function filterWorkoutsByType(type) {
  const cards = document.querySelectorAll('.workout-list-card');
  cards.forEach(card => {
    // Each card has a class like "workout-icon running"
    const iconDiv = card.querySelector('.workout-icon');
    if (!type || iconDiv.classList.contains(type)) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}

// Helper functions
function showPage(pageId) {
  console.log('Showing page:', pageId);
  
  const page = document.getElementById(pageId);
  if (!page) {
    console.error(`Page with ID "${pageId}" not found!`);
    return;
  }
  
  document.querySelectorAll('.page-content').forEach(p => {
    p.classList.remove('active');
  });
  
  page.classList.add('active');
}

function showElements(selectors) {
  selectors.forEach(selector => {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
      el.style.display = '';
    });
  });
}

function hideElements(selectors) {
  selectors.forEach(selector => {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
      el.style.display = 'none';
    });
  });
}

// Set current date for form date fields
document.addEventListener('DOMContentLoaded', function() {
  const today = new Date().toISOString().split('T')[0];
  
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach(input => {
    if (!input.value) {
      input.value = today;
    }
  });
});

async function fetchAndRenderMetrics() {
  try {
    const token = localStorage.getItem('jwtToken');
    const response = await fetch(`${BASE_URL}/api/get_metrics.php`, {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    });
    const result = await response.json();

    const tableBody = document.querySelector('#healthMetricsPage .metrics-table tbody');
    tableBody.innerHTML = '';

    if (response.ok && result.success && result.current.length > 0) {
      result.current.forEach(metric => {
        const prev = result.previous[metric.type];
        let currentValue = '';
        let prevValue = '';
        let change = '';
        let badgeClass = 'success';
        let statusIcon = '<i class="fas fa-chart-line"></i>';
        let statusClass = 'improving';

        // Format value based on type
        if (metric.type === 'blood_pressure') {
          currentValue = `${metric.systolic}/${metric.diastolic}`;
          if (prev) prevValue = `${prev.systolic}/${prev.diastolic}`;
          change = prev ? `${metric.systolic - prev.systolic}/${metric.diastolic - prev.diastolic}` : '-';
        } else {
          currentValue = metric.value !== null ? metric.value : '-';
          prevValue = prev && prev.value !== null ? prev.value : '-';
          change = prev && prev.value !== null && metric.value !== null ? (metric.value - prev.value).toFixed(1) : '-';
        }

        // Badge & status
        if (metric.status_color === 'danger') {
          badgeClass = 'danger';
          statusClass = 'declining';
          statusIcon = '<i class="fas fa-arrow-down"></i>';
        } else if (metric.status_color === 'warning') {
          badgeClass = 'warning';
          statusClass = 'attention';
          statusIcon = '<i class="fas fa-exclamation-triangle"></i>';
        }

        // Format date
        let lastUpdatedStr = '-';
        if (metric.last_updated) {
          const lastUpdated = new Date(metric.last_updated);
          lastUpdatedStr = lastUpdated.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        } else if (metric.date) {
          lastUpdatedStr = metric.date;
        }

        tableBody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>
              <div class="metric-info">
                <div class="metric-icon ${statusClass}">
                  <i class="fas fa-${getMetricIcon(metric.type)}"></i>
                </div>
                <div>
                  <div class="metric-name">${getMetricName(metric.type)}</div>
                  <div class="metric-unit">${getMetricUnit(metric.type)}</div>
                </div>
              </div>
            </td>
            <td>${currentValue}</td>
            <td>${prevValue}</td>
            <td><span class="badge ${badgeClass}">${change}</span></td>
            <td><span class="status ${statusClass}">${statusIcon} ${metric.status || '-'}</span></td>
            <td>${lastUpdatedStr}</td>
          </tr>
        `);
      });
    } else {
      // Tampilkan data dummy/statis jika database kosong
      tableBody.innerHTML = `
        <tr>
          <td>
            <div class="metric-info">
              <div class="metric-icon improving"><i class="fas fa-heartbeat"></i></div>
              <div>
                <div class="metric-name">Resting Heart Rate</div>
                <div class="metric-unit">bpm</div>
              </div>
            </div>
          </td>
          <td>68</td>
          <td>72</td>
          <td><span class="badge success">-4</span></td>
          <td><span class="status improving"><i class="fas fa-chart-line"></i> Improving</span></td>
          <td>May 8, 2023</td>
        </tr>
        <tr>
          <td>
            <div class="metric-info">
              <div class="metric-icon declining"><i class="fas fa-weight"></i></div>
              <div>
                <div class="metric-name">Weight</div>
                <div class="metric-unit">kg</div>
              </div>
            </div>
          </td>
          <td>75.5</td>
          <td>76.2</td>
          <td><span class="badge danger">-0.7</span></td>
          <td><span class="status declining"><i class="fas fa-arrow-down"></i> Deteriorate</span></td>
          <td>May 8, 2023</td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Error fetching metrics:', error);
  }
}

// Helper for icon, name, unit
function getMetricIcon(type) {
  switch (type) {
    case 'heart_rate': return 'heartbeat';
    case 'weight': return 'weight';
    case 'sleep': return 'moon';
    case 'hydration': return 'tint';
    case 'blood_pressure': return 'heart';
    default: return 'chart-bar';
  }
}
function getMetricName(type) {
  switch (type) {
    case 'heart_rate': return 'Resting Heart Rate';
    case 'weight': return 'Weight';
    case 'sleep': return 'Sleep Duration';
    case 'hydration': return 'Water Intake';
    case 'blood_pressure': return 'Blood Pressure';
    default: return type;
  }
}
function getMetricUnit(type) {
  switch (type) {
    case 'heart_rate': return 'bpm';
    case 'weight': return 'kg';
    case 'sleep': return 'hours';
    case 'hydration': return 'liters';
    case 'blood_pressure': return 'mmHg';
    default: return '';
  }
}

async function fetchAndRenderGoals() {
  const token = localStorage.getItem('jwtToken');
  const res = await fetch(`${BASE_URL}/api/get_goals.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const data = await res.json();
  const goalsList = document.querySelector('.goals-list');
  goalsList.innerHTML = '';
  data.current.forEach(goal => {
    goalsList.insertAdjacentHTML('beforeend', `
      <div class="goal-item">
        <div class="goal-info">
          <h4>${goal.description}</h4>
          <p>Current: ${goal.current_value ?? '-'}${goal.unit ? ' ' + goal.unit : ''}</p>
        </div>
        <div class="goal-progress">
          <div class="progress-container">
            <div class="progress-bar" style="width: ${goal.progress}%;"></div>
          </div>
          <p class="progress-text">${goal.progress}% achieved</p>
        </div>
      </div>
    `);
  });
  await fetch(`${BASE_URL}/api/update_goal_progress.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  fetchAndRenderGoals();
}

async function fetchAndRenderTodayActivity() {
  const token = localStorage.getItem('jwtToken');
  const res = await fetch(`${BASE_URL}/api/get_today_activity.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const data = await res.json();

  // Set your daily goals
  const stepsGoal = 7000;
  const caloriesGoal = 2000;
  const minutesGoal = 70;

  // Calculate percentages
  const stepsPct = Math.min(100, Math.round((data.steps / stepsGoal) * 100));
  const caloriesPct = Math.min(100, Math.round((data.calories / caloriesGoal) * 100));
  const minutesPct = Math.min(100, Math.round((data.active_minutes / minutesGoal) * 100));

  // Update DOM
  document.querySelector('.activity-card .activity-value').textContent = data.steps;
  document.querySelectorAll('.activity-card')[0].querySelector('.progress-bar').style.width = stepsPct + '%';
  document.querySelectorAll('.activity-card')[0].querySelector('.progress-text').textContent = `${stepsPct}% of daily goal`;

  document.querySelectorAll('.activity-card')[1].querySelector('.activity-value').textContent = data.calories;
  document.querySelectorAll('.activity-card')[1].querySelector('.progress-bar').style.width = caloriesPct + '%';
  document.querySelectorAll('.activity-card')[1].querySelector('.progress-text').textContent = `${caloriesPct}% of daily goal`;

  document.querySelectorAll('.activity-card')[2].querySelector('.activity-value').textContent = data.active_minutes;
  document.querySelectorAll('.activity-card')[2].querySelector('.progress-bar').style.width = minutesPct + '%';
  document.querySelectorAll('.activity-card')[2].querySelector('.progress-text').textContent = `${minutesPct}% of daily goal`;
}

async function fetchAndRenderDashboardMetrics() {
  const token = localStorage.getItem('jwtToken');
  const response = await fetch(`${BASE_URL}/api/get_metrics.php`, {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const result = await response.json();

  const tableBody = document.querySelector('#dashboardMetricsTable tbody');
  tableBody.innerHTML = '';

  if (response.ok && result.success && result.current.length > 0) {
    result.current.forEach(metric => {
      const prev = result.previous[metric.type];
      let currentValue = '';
      let prevValue = '';
      let change = '';
      let badgeClass = 'success';
      let statusIcon = '<i class="fas fa-chart-line"></i>';
      let statusClass = 'improving';

      // Format value based on type
      if (metric.type === 'blood_pressure') {
        currentValue = `${metric.systolic}/${metric.diastolic}`;
        if (prev) prevValue = `${prev.systolic}/${prev.diastolic}`;
        change = prev ? `${metric.systolic - prev.systolic}/${metric.diastolic - prev.diastolic}` : '-';
      } else {
        currentValue = metric.value !== null ? metric.value : '-';
        prevValue = prev && prev.value !== null ? prev.value : '-';
        change = prev && prev.value !== null && metric.value !== null ? (metric.value - prev.value).toFixed(1) : '-';
      }

      // Badge & status
      if (metric.status_color === 'danger') {
        badgeClass = 'danger';
        statusClass = 'declining';
        statusIcon = '<i class="fas fa-arrow-down"></i>';
      } else if (metric.status_color === 'warning') {
        badgeClass = 'warning';
        statusClass = 'attention';
        statusIcon = '<i class="fas fa-exclamation-triangle"></i>';
      }

      tableBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td>
            <div class="metric-info">
              <div class="metric-icon ${statusClass}">
                <i class="fas fa-${getMetricIcon(metric.type)}"></i>
              </div>
              <div>
                <div class="metric-name">${getMetricName(metric.type)}</div>
                <div class="metric-unit">${getMetricUnit(metric.type)}</div>
              </div>
            </div>
          </td>
          <td>${currentValue}</td>
          <td>${prevValue}</td>
          <td><span class="badge ${badgeClass}">${change}</span></td>
          <td><span class="status ${statusClass}">${statusIcon} ${metric.status || '-'}</span></td>
        </tr>
      `);
    });
  } else {
    tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No health metrics yet.</td></tr>`;
  }
}

// Ambil token dari URL jika ada
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');
if (token) {
  localStorage.setItem('jwtToken', token);
  localStorage.setItem('isLoggedIn', 'true');
  // Optionally: fetch user info with token, lalu update UI
  window.history.replaceState({}, document.title, "/"); // Hapus token dari URL
  showPage('dashboardPage');
  showElements(['.sidebar', '.mobile-header', '.mobile-bottom-nav']);
}

function updateUsernameUI(nameOrUsername) {
  document.querySelectorAll('.username').forEach(el => {
    el.textContent = nameOrUsername;
  });
  const initials = (nameOrUsername || '').substring(0, 2).toUpperCase();
  document.querySelectorAll('.avatar').forEach(el => {
    el.textContent = initials;
  });
}

