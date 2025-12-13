/**
 * Thailand Election Map - Interactive Map Component
 * แผนที่เลือกตั้งไทยแบบ Interactive
 */

// District data cache
let districtDataCache = {};
let selectedProvinceId = null;

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  loadThailandMap();
  initializeMapLegend();
  initializePanelClose();
});

/**
 * Load Thailand SVG map
 */
async function loadThailandMap() {
  const mapContainer = document.getElementById('thailandMap');
  if (!mapContainer) return;

  try {
    const response = await fetch('assets/thailand-map.svg');
    const svgText = await response.text();
    mapContainer.innerHTML = svgText;

    // Initialize map after SVG is loaded
    initializeMapInteraction();
    updateMapColors();
  } catch (error) {
    console.error('Error loading Thailand map:', error);
    mapContainer.innerHTML = '<p style="text-align: center; padding: 2rem;">ไม่สามารถโหลดแผนที่ได้</p>';
  }
}

/**
 * Initialize map interactions (hover, click)
 */
function initializeMapInteraction() {
  const provinces = document.querySelectorAll('.province');
  const tooltip = document.getElementById('mapTooltip');
  const mapWrapper = document.getElementById('mapWrapper');

  provinces.forEach(province => {
    // Hover - show tooltip
    province.addEventListener('mouseenter', function(e) {
      const provinceId = this.id;
      showTooltip(provinceId, e);
    });

    province.addEventListener('mousemove', function(e) {
      moveTooltip(e);
    });

    province.addEventListener('mouseleave', function() {
      hideTooltip();
    });

    // Click - show details
    province.addEventListener('click', function() {
      const provinceId = this.id;
      selectProvince(provinceId);
    });
  });
}

/**
 * Show tooltip with province info
 */
function showTooltip(provinceId, event) {
  const tooltip = document.getElementById('mapTooltip');
  const data = provinceMapData[currentYear]?.[provinceId];

  if (!data) {
    tooltip.style.display = 'none';
    return;
  }

  document.getElementById('tooltipTitle').textContent = data.name;
  document.getElementById('tooltipParty').textContent = 'พรรค' + data.party;
  document.getElementById('tooltipParty').style.color = data.color;

  if (data.seats > 0) {
    document.getElementById('tooltipStats').textContent = `${data.won}/${data.seats} เขต`;
  } else {
    document.getElementById('tooltipStats').textContent = formatVotes(data.votes) + ' คะแนน';
  }

  tooltip.style.display = 'block';
  moveTooltip(event);
}

/**
 * Move tooltip to follow cursor
 */
function moveTooltip(event) {
  const tooltip = document.getElementById('mapTooltip');
  const mapWrapper = document.getElementById('mapWrapper');
  const rect = mapWrapper.getBoundingClientRect();

  let x = event.clientX - rect.left + 15;
  let y = event.clientY - rect.top + 15;

  // Keep tooltip within bounds
  if (x + tooltip.offsetWidth > rect.width) {
    x = event.clientX - rect.left - tooltip.offsetWidth - 15;
  }
  if (y + tooltip.offsetHeight > rect.height) {
    y = event.clientY - rect.top - tooltip.offsetHeight - 15;
  }

  tooltip.style.left = x + 'px';
  tooltip.style.top = y + 'px';
}

/**
 * Hide tooltip
 */
function hideTooltip() {
  const tooltip = document.getElementById('mapTooltip');
  tooltip.style.display = 'none';
}

/**
 * Select a province and show details
 */
async function selectProvince(provinceId) {
  // Update selection state
  const provinces = document.querySelectorAll('.province');
  provinces.forEach(p => p.classList.remove('selected'));

  const selectedProvince = document.getElementById(provinceId);
  if (selectedProvince) {
    selectedProvince.classList.add('selected');
  }

  selectedProvinceId = provinceId;

  // Get province data
  const mapInfo = provinceMapData[currentYear]?.[provinceId];
  if (!mapInfo) {
    showPanelError('ไม่พบข้อมูลจังหวัดนี้');
    return;
  }

  // Update panel header
  document.getElementById('panelTitle').textContent = mapInfo.name;

  // Load district data
  const panelContent = document.getElementById('panelContent');
  panelContent.innerHTML = '<p class="loading">กำลังโหลดข้อมูล...</p>';

  try {
    const districtData = await loadDistrictData(currentYear, provinceId);
    renderDistrictDetails(districtData, mapInfo);
  } catch (error) {
    console.error('Error loading district data:', error);
    showPanelError('ไม่สามารถโหลดข้อมูลเขตได้');
  }
}

/**
 * Load district data for a province
 */
async function loadDistrictData(year, provinceId) {
  const cacheKey = `${year}_${provinceId}`;

  if (districtDataCache[cacheKey]) {
    return districtDataCache[cacheKey];
  }

  // Load full district data for the year
  if (!districtDataCache[year]) {
    const response = await fetch(`data/districts-${year}.json`);
    districtDataCache[year] = await response.json();
  }

  return districtDataCache[year][provinceId] || null;
}

/**
 * Render district details in panel
 */
function renderDistrictDetails(districtData, mapInfo) {
  const panelContent = document.getElementById('panelContent');

  if (!districtData) {
    panelContent.innerHTML = `
            <div class="province-summary">
                <div class="summary-item">
                    <span class="summary-label">พรรคที่ชนะ:</span>
                    <span class="summary-value" style="color: ${mapInfo.color}">พรรค${mapInfo.party}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">คะแนนรวม:</span>
                    <span class="summary-value">${formatVotes(mapInfo.votes)}</span>
                </div>
            </div>
            <p class="no-districts">ไม่มีข้อมูลรายเขตสำหรับปีนี้</p>
        `;
    return;
  }

  let html = `
        <div class="province-summary">
            <div class="summary-item">
                <span class="summary-label">พรรคที่ชนะ:</span>
                <span class="summary-value" style="color: ${mapInfo.color}">พรรค${mapInfo.party}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">ชนะ:</span>
                <span class="summary-value">${mapInfo.won}/${mapInfo.seats} เขต</span>
            </div>
        </div>
    `;

  // If has districts (2566, 2562)
  if (districtData.districts && districtData.districts.length > 0) {
    html += '<table class="district-table">';
    html += '<thead><tr><th>เขต</th><th>ผู้ชนะ</th><th>พรรค</th><th>คะแนน</th></tr></thead>';
    html += '<tbody>';

    districtData.districts.forEach(d => {
      html += `
                <tr>
                    <td class="district-num">${d.district}</td>
                    <td class="district-winner">${d.winner}</td>
                    <td class="district-party">
                        <span class="party-dot" style="background-color: ${d.color}"></span>
                        ${d.winnerParty}
                    </td>
                    <td class="district-votes">${d.winnerVotes.toLocaleString()}</td>
                </tr>
            `;
    });

    html += '</tbody></table>';
  }
  // If has parties breakdown (2554)
  else if (districtData.parties && districtData.parties.length > 0) {
    html += '<table class="district-table">';
    html += '<thead><tr><th>#</th><th>พรรค</th><th>คะแนน</th></tr></thead>';
    html += '<tbody>';

    districtData.parties.forEach((p, i) => {
      html += `
                <tr>
                    <td class="district-num">${i + 1}</td>
                    <td class="district-party">${p.party}</td>
                    <td class="district-votes">${p.votes.toLocaleString()}</td>
                </tr>
            `;
    });

    html += '</tbody></table>';
  }

  panelContent.innerHTML = html;
}

/**
 * Show error in panel
 */
function showPanelError(message) {
  const panelContent = document.getElementById('panelContent');
  panelContent.innerHTML = `<p class="panel-error">${message}</p>`;
}

/**
 * Update map colors based on current year
 */
function updateMapColors() {
  const yearData = provinceMapData[currentYear];
  if (!yearData) return;

  Object.entries(yearData).forEach(([provinceId, data]) => {
    const province = document.getElementById(provinceId);
    if (province) {
      province.style.fill = data.color;
    }
  });

  // Update legend
  initializeMapLegend();
}

/**
 * Initialize map legend showing party colors
 */
function initializeMapLegend() {
  const legendContainer = document.getElementById('mapLegend');
  if (!legendContainer) return;

  const yearData = provinceMapData[currentYear];
  if (!yearData) return;

  // Count provinces per party
  const partyCount = {};
  Object.values(yearData).forEach(data => {
    const key = data.party;
    if (!partyCount[key]) {
      partyCount[key] = {count: 0, color: data.color};
    }
    partyCount[key].count++;
  });

  // Sort by count
  const sorted = Object.entries(partyCount).sort((a, b) => b[1].count - a[1].count);

  let html = '';
  sorted.slice(0, 8).forEach(([party, info]) => {
    html += `
            <div class="legend-item">
                <span class="legend-color" style="background-color: ${info.color}"></span>
                <span class="legend-text">${party} (${info.count})</span>
            </div>
        `;
  });

  legendContainer.innerHTML = html;
}

/**
 * Initialize panel close button
 */
function initializePanelClose() {
  const closeBtn = document.getElementById('panelClose');
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      // Deselect province
      const provinces = document.querySelectorAll('.province');
      provinces.forEach(p => p.classList.remove('selected'));
      selectedProvinceId = null;

      // Reset panel
      document.getElementById('panelTitle').textContent = 'เลือกจังหวัดบนแผนที่';
      document.getElementById('panelContent').innerHTML =
        '<p class="panel-placeholder">คลิกจังหวัดบนแผนที่เพื่อดูผลการเลือกตั้งแต่ละเขต</p>';
    });
  }
}

/**
 * Format vote numbers
 */
function formatVotes(num) {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + ' ล้าน';
  }
  return num.toLocaleString();
}

// Listen for year changes (from main script.js)
document.addEventListener('DOMContentLoaded', function() {
  // Override year button click to also update map
  const yearButtons = document.querySelectorAll('.year-btn');
  yearButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      // Wait for currentYear to be updated by main script
      setTimeout(() => {
        updateMapColors();
        // Clear selection
        if (selectedProvinceId) {
          document.querySelectorAll('.province').forEach(p => p.classList.remove('selected'));
          document.getElementById('panelTitle').textContent = 'เลือกจังหวัดบนแผนที่';
          document.getElementById('panelContent').innerHTML =
            '<p class="panel-placeholder">คลิกจังหวัดบนแผนที่เพื่อดูผลการเลือกตั้งแต่ละเขต</p>';
          selectedProvinceId = null;
        }
      }, 50);
    });
  });
});
