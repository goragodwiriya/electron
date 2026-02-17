// Thai Election Hub - JavaScript
// ใช้ข้อมูลจริงจาก XLS ของ กกต. เท่านั้น

// Current selected year
let currentYear = 2569;

// Election dates (hardcoded because XLS doesn't contain dates)
const electionDates = {
  2569: '9 ก.พ. 2569',
  2566: '14 พ.ค. 2566',
  2562: '24 มี.ค. 2562',
  2554: '3 ก.ค. 2554'
};

// Get election data for specific year
function getElectionDataForYear(year) {
  if (typeof electionDataFromXLS === 'undefined') {
    return {
      electionDate: electionDates[year] || `พ.ศ. ${year}`,
      totalVotes: "ไม่มีข้อมูล",
      turnout: "-",
      parties: [],
      hasData: false
    };
  }

  const data = electionDataFromXLS[year];

  if (!data) {
    return {
      electionDate: electionDates[year] || `พ.ศ. ${year}`,
      totalVotes: "ไม่มีข้อมูล",
      turnout: "-",
      parties: [],
      hasData: false
    };
  }

  // Handle national as either object or empty array
  const national = Array.isArray(data.national) && data.national.length === 0
    ? {}
    : (data.national || {});

  const hasParties = data.parties && data.parties.length > 0;

  if (!hasParties) {
    return {
      electionDate: data.date || electionDates[year] || `พ.ศ. ${year}`,
      totalVotes: national.actualVoters
        ? (national.actualVoters / 1000000).toFixed(1) + " ล้าน"
        : "ไม่มีข้อมูล",
      turnout: national.turnoutPercentage
        ? national.turnoutPercentage.toFixed(2) + "%"
        : "-",
      parties: [],
      regions: data.regions || [],
      hasData: false,
      message: "ไม่มีข้อมูลพรรคการเมืองในไฟล์ XLS"
    };
  }

  // Calculate total seats
  const totalSeats = data.parties.reduce((sum, p) => sum + (p.seats || 0), 0);

  return {
    electionDate: data.date || electionDates[year] || `พ.ศ. ${year}`,
    totalVotes: national.actualVoters
      ? (national.actualVoters / 1000000).toFixed(1) + " ล้าน"
      : totalSeats > 0 ? `${totalSeats} ที่นั่ง` : "-",
    turnout: national.turnoutPercentage
      ? national.turnoutPercentage.toFixed(2) + "%"
      : "-",
    eligibleVoters: national.eligibleVoters
      ? (national.eligibleVoters / 1000000).toFixed(1) + " ล้าน"
      : "-",
    totalSeats: totalSeats,
    regions: data.regions || [],
    parties: data.parties.map(party => ({
      ...party,
      voteDisplay: party.seatPercentage ? party.seatPercentage.toFixed(1) + "%" : "-"
    })),
    hasData: true
  };
}

// Policy Comparison Data
// หมายเหตุ: ไม่มีข้อมูลนโยบายใน XLS ของ กกต.
// ส่วนนี้จะถูกซ่อนเมื่อไม่มีข้อมูล
const policyComparisons = [];

// Update stats banner for selected year
function updateStatsBanner(yearData) {
  const banner = document.getElementById('statsBanner');
  if (!banner) return;

  banner.innerHTML = `
        <div class="stat-item">
            <span class="stat-label">อัตราการมีส่วนร่วม:</span>
            <span class="stat-value">${yearData.turnout}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">ผู้ใช้สิทธิ:</span>
            <span class="stat-value">${yearData.totalVotes}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">วันเลือกตั้ง:</span>
            <span class="stat-value">${yearData.electionDate}</span>
        </div>
    `;
}
// Update results section title for selected year
function updateResultsSection(year) {
  const title = document.getElementById('resultsTitle');
  const subtitle = document.getElementById('resultsSubtitle');

  if (title) {
    title.textContent = `ผลการเลือกตั้ง พ.ศ. ${year}`;
  }

  if (subtitle) {
    const dates = {
      2569: 'ข้อมูลการเลือกตั้งสมาชิกสภาผู้แทนราษฎร 9 กุมภาพันธ์ 2569',
      2566: 'ข้อมูลการเลือกตั้งสมาชิกสภาผู้แทนราษฎร 14 พฤษภาคม 2566',
      2562: 'ข้อมูลการเลือกตั้งสมาชิกสภาผู้แทนราษฎร 24 มีนาคม 2562',
      2554: 'ข้อมูลการเลือกตั้งสมาชิกสภาผู้แทนราษฎร 3 กรกฎาคม 2554'
    };
    subtitle.textContent = dates[year] || `ข้อมูลการเลือกตั้ง พ.ศ. ${year}`;
  }
}

// Initialize year selector
function initializeYearSelector() {
  const yearButtons = document.querySelectorAll('.year-btn');

  yearButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      // Update active state
      yearButtons.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      // Get selected year
      currentYear = parseInt(this.dataset.year);

      // Reload data for selected year
      const yearData = getElectionDataForYear(currentYear);

      // Update all components
      updateResultsSection(currentYear);
      updateStatsBanner(yearData);
      initializeParliamentChart(yearData);
      initializeVoteBars(yearData);
      initializePartyStats(yearData);
      initializePartiesGrid(yearData);
      addChartLegend(yearData);

      // Show notification
      console.log(`Switched to year: ${currentYear}`);
    });
  });
}

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
  const initialData = getElectionDataForYear(currentYear);

  initializeYearSelector();
  updateStatsBanner(initialData);
  initializeParliamentChart(initialData);
  initializeVoteBars(initialData);
  initializePartyStats(initialData);
  initializePartiesGrid(initialData);
  addChartLegend(initialData);
  initializeComparisonTable();
  initializeSearch();
  setupSmoothScrolling();
});

// Parliament Chart (Donut Chart)
function initializeParliamentChart(yearData) {
  const canvas = document.getElementById('parliamentCanvas');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  const centerX = canvas.width / 2;
  const centerY = canvas.height / 2;
  const radius = Math.min(centerX, centerY) - 20;
  const innerRadius = radius * 0.6;

  const parties = yearData?.parties || [];
  const totalSeats = parties.reduce((sum, p) => sum + (p.seats || 0), 0) || 500;

  // Clear canvas
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  let currentAngle = -Math.PI / 2; // Start from top

  parties.forEach((party, index) => {
    const angle = (party.seats / totalSeats) * 2 * Math.PI;

    // Draw outer arc
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + angle);
    ctx.arc(centerX, centerY, innerRadius, currentAngle + angle, currentAngle, true);
    ctx.closePath();
    ctx.fillStyle = party.color;
    ctx.fill();

    currentAngle += angle;
  });

  // Draw center circle
  ctx.beginPath();
  ctx.arc(centerX, centerY, innerRadius, 0, 2 * Math.PI);
  ctx.fillStyle = '#FFFFFF';
  ctx.fill();

  // Draw center text
  ctx.fillStyle = '#111827';
  ctx.font = 'bold 24px IBM Plex Sans Thai';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(totalSeats.toString(), centerX, centerY - 10);
  ctx.font = '14px IBM Plex Sans Thai';
  ctx.fillText('ที่นั่ง', centerX, centerY + 10);
}

function initializeVoteBars(yearData) {
  const voteBarsContainer = document.getElementById('voteBars');
  if (!voteBarsContainer) return;

  voteBarsContainer.innerHTML = '';
  const parties = yearData?.parties || [];

  parties.forEach(party => {
    const voteBar = document.createElement('div');
    voteBar.className = 'vote-bar';

    voteBar.innerHTML = `
            <div class="vote-bar-label">
                <span class="party-name">${party.name}</span>
                <span class="vote-percentage">${party.votePercentage}</span>
            </div>
            <div class="vote-bar-track">
                <div class="vote-bar-fill" style="width: ${party.votePercentage}; background-color: ${party.color};"></div>${party.votes}
            </div>
        `;

    voteBarsContainer.appendChild(voteBar);
  });
}

function initializePartyStats(yearData) {
  const statsGrid = document.getElementById('partyStatsGrid');
  if (!statsGrid) return;

  statsGrid.innerHTML = '';
  const parties = yearData?.parties || [];

  parties.forEach(party => {
    const statCard = document.createElement('div');
    statCard.className = 'party-stat-card';
    if (party.status === 'dissolved') {
      statCard.classList.add('dissolved');
    }

    const logoText = party.nameEn.split(' ').map(word => word[0]).join('').substring(0, 3);

    statCard.innerHTML = `
            <div class="party-header">
                <div class="party-logo" style="background-color: ${party.color};">${logoText}</div>
                <div class="party-info">
                    <h4>${party.name}</h4>
                    <div class="party-leader">${party.leader}</div>
                </div>
            </div>
            <div class="party-metrics">
                <div class="metric">
                    <span class="metric-value">${party.seats}</span>
                    <span class="metric-label">ที่นั่ง</span>
                </div>
                <div class="metric">
                    <span class="metric-value">${party.votes}</span>
                    <span class="metric-label">คะแนนเสียง</span>
                </div>
            </div>
            ${party.status === 'dissolved' ? '<div class="dissolved-badge">ยุบเมื่อ ' + party.dissolutionDate + '</div>' : ''}
        `;

    statsGrid.appendChild(statCard);
  });
}

// Parties Grid
function initializePartiesGrid(yearData) {
  const partiesGrid = document.getElementById('partiesGrid');
  if (!partiesGrid) return;

  partiesGrid.innerHTML = '';
  const parties = yearData?.parties || [];

  parties.forEach(party => {
    const partyCard = document.createElement('div');
    partyCard.className = 'party-card';
    if (party.status === 'dissolved') {
      partyCard.classList.add('dissolved');
    }

    // Use first letters of Thai name if no English name
    const logoText = party.nameEn
      ? party.nameEn.split(' ').map(word => word[0]).join('').substring(0, 3)
      : party.name.replace('พรรค', '').substring(0, 2);

    // Generate policy tags
    const policies = party.policies || [];
    const policyTagsHtml = policies.length > 0
      ? `<div class="policy-tags">${policies.map(p => `<span class="policy-tag">${p}</span>`).join('')}</div>`
      : '';

    // Leader display
    const leaderHtml = party.leader
      ? `<div class="party-leader">👤 ${party.leader}</div>`
      : '';

    partyCard.innerHTML = `
            <div class="party-header">
                <div class="party-logo" style="background-color: ${party.color};">${logoText}</div>
                <div class="party-info">
                    <h4>${party.name}</h4>
                    ${leaderHtml}
                </div>
            </div>
            <div class="party-metrics">
                <div class="metric">
                    <span class="metric-value">${party.seats}</span>
                    <span class="metric-label">ที่นั่ง</span>
                </div>
                <div class="metric">
                    <span class="metric-value">${party.constituencySeats || 0}</span>
                    <span class="metric-label">แบ่งเขต</span>
                </div>
                <div class="metric">
                    <span class="metric-value">${party.partyListSeats || 0}</span>
                    <span class="metric-label">บัญชีรายชื่อ</span>
                </div>
            </div>
            ${policyTagsHtml}
            ${party.status === 'dissolved' ? '<div class="dissolved-badge">ยุบเมื่อ ' + (party.dissolutionDate || '-') + '</div>' : ''}
        `;

    partiesGrid.appendChild(partyCard);
  });
}

// Comparison Table
function initializeComparisonTable() {
  const headerRow = document.getElementById('comparisonHeader');
  const body = document.getElementById('comparisonBody');
  const comparisonSection = document.querySelector('.comparison-section');

  if (!headerRow || !body) return;

  // Hide section if no policy data (XLS ไม่มีข้อมูลนโยบาย)
  if (policyComparisons.length === 0) {
    if (comparisonSection) {
      comparisonSection.style.display = 'none';
    }
    return;
  }

  // Create header
  const yearData = getElectionDataForYear(currentYear);
  headerRow.innerHTML = '<th class="policy-issue">ประเด็นนโยบาย</th>';
  yearData.parties.forEach(party => {
    headerRow.innerHTML += `<th class="party-column">${party.name}</th>`;
  });

  // Create body
  body.innerHTML = '';
  policyComparisons.forEach(comparison => {
    const row = document.createElement('tr');
    let rowHTML = `<td class="policy-issue"><strong>${comparison.issue}</strong></td>`;

    yearData.parties.forEach(party => {
      const partyData = comparison.parties[party.id];
      if (partyData) {
        const statusClass = partyData.status.replace('-', '');
        const iconClass = partyData.status === 'strong-support' ? 'status-support' :
          partyData.status === 'support' ? 'status-support' :
            partyData.status === 'oppose' ? 'status-oppose' :
              'status-neutral';

        rowHTML += `
                    <td class="policy-cell">
                        <div class="policy-status">
                            <div class="status-icon ${iconClass}"></div>
                            <div>
                                <div>${partyData.description}</div>
                            </div>
                        </div>
                    </td>
                `;
      } else {
        rowHTML += '<td class="policy-cell">-</td>';
      }
    });

    row.innerHTML = rowHTML;
    body.appendChild(row);
  });
}

// Search functionality
function initializeSearch() {
  const searchInput = document.querySelector('.search-input');
  const searchButton = document.querySelector('.search-button');

  if (!searchInput || !searchButton) return;

  function performSearch() {
    const query = searchInput.value.toLowerCase().trim();
    if (!query) return;

    // Get current year data
    const yearData = getElectionDataForYear(currentYear);

    // Search in parties (only use fields available in XLS)
    const matchingParties = yearData.parties.filter(party =>
      party.name?.toLowerCase().includes(query) ||
      party.nameEn?.toLowerCase().includes(query) ||
      party.id?.toLowerCase().includes(query)
    );

    // Display results
    if (matchingParties.length > 0) {
      const partyNames = matchingParties.slice(0, 5).map(p => p.name).join(', ');
      alert(`พบผลการค้นหา: ${matchingParties.length} พรรค\n\n${partyNames}${matchingParties.length > 5 ? '...' : ''}`);
    } else {
      alert('ไม่พบผลการค้นหาสำหรับ: ' + query);
    }
  }

  searchButton.addEventListener('click', performSearch);
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      performSearch();
    }
  });
}

// Smooth scrolling for navigation links
function setupSmoothScrolling() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
}

// Add chart legend
function addChartLegend(yearData) {
  const legendContainer = document.getElementById('chartLegend');
  if (!legendContainer) return;

  legendContainer.innerHTML = '';
  const parties = yearData?.parties || [];

  parties.forEach(party => {
    const legendItem = document.createElement('div');
    legendItem.className = 'legend-item';

    legendItem.innerHTML = `
            <div class="legend-color" style="background-color: ${party.color};"></div>
            <div class="legend-text">
                <strong>${party.name}</strong><br>
                ${party.seats} ที่นั่ง (${party.votePercentage})
            </div>
        `;

    legendContainer.appendChild(legendItem);
  });
}

// Add responsive chart handling
function handleResponsiveCharts() {
  const canvas = document.getElementById('parliamentCanvas');
  if (!canvas) return;

  function resizeCanvas() {
    const container = canvas.parentElement;
    const containerWidth = container.clientWidth;
    const size = Math.min(containerWidth - 40, 300);

    canvas.width = size;
    canvas.height = size;

    // Redraw chart with current year data
    const yearData = getElectionDataForYear(currentYear);
    initializeParliamentChart(yearData);
  }

  // Resize on window resize
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();
}

// Initialize responsive charts
document.addEventListener('DOMContentLoaded', handleResponsiveCharts);