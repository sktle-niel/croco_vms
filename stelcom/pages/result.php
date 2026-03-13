<!-- Results Page -->
<?php
require_once __DIR__ . '/../../backend/include.php';

$stats = getDashboardStats();
$totalVoters = $stats['totalVoters'];
$candidates = getResultsCandidates();
?>

<div class="results-page">

    <div class="results-header">
        <h2 class="results-title">Voting Results</h2>
        <button class="export-btn" onclick="generatePDF()" aria-label="Export voting results as PDF">
            <i class="fas fa-download"></i>
            Export PDF
        </button>
    </div>

    <div class="results-container">
        <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i class="fas fa-user-tie"></i>
            <p>No candidates found.</p>
        </div>
        <?php else: ?>
            <?php foreach ($candidates as $c):
                $barPct = $totalVoters > 0 ? round(($c['vote_count'] / $totalVoters) * 100) : 0;
            ?>
            <div class="candidate-card">
                <?php if (!empty($c['cand_photo'])): ?>
                    <img src="../../<?php echo htmlspecialchars($c['cand_photo']); ?>"
                         alt="<?php echo htmlspecialchars($c['cand_fullname']); ?>"
                         class="candidate-image"
                         style="width:56px;height:56px;">
                <?php else: ?>
                    <div class="candidate-image" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;">
                        <i class="fas fa-user" style="font-size:2rem;color:#aaa;"></i>
                    </div>
                <?php endif; ?>
                <div class="candidate-info">
                    <div class="candidate-name"><?php echo htmlspecialchars($c['cand_fullname']); ?></div>
                    <div class="candidate-position"><?php echo htmlspecialchars($c['cand_position']); ?></div>
                    <?php if (!empty($c['cand_partylist'])): ?>
                        <div class="candidate-partylist"><?php echo htmlspecialchars($c['cand_partylist']); ?></div>
                    <?php endif; ?>
                    <div class="vote-count"><?php echo number_format($c['vote_count']); ?> votes</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $barPct; ?>%;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    const candidateData = <?php echo json_encode(array_values($candidates ?? [])); ?>;
    const totalVoters   = <?php echo json_encode((int)($totalVoters ?? 0)); ?>;
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
    function generatePDF() {

        /* ── group & sort by position ── */
        const groups = {};
        candidateData.forEach(c => {
            const pos = (c.cand_position || 'Other').trim();
            if (!groups[pos]) groups[pos] = [];
            groups[pos].push(c);
        });
        Object.values(groups).forEach(g => g.sort((a, b) => b.vote_count - a.vote_count));

        function escHtml(str) {
            return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        }

        /* ── build one formal B&W table per position ── */
        function buildTable(position, rows) {
            let tableRows = '';
            rows.forEach((c, i) => {
                const rank    = i + 1;
                const pct     = totalVoters > 0
                                  ? ((c.vote_count / totalVoters) * 100).toFixed(2)
                                  : '0.00';
                const isWinner = i === 0;

                tableRows += `
                <tr>
                    <td style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.85rem;">${rank}</td>
                    <td style="padding:8px 12px;border:1px solid #000;font-size:0.85rem;font-weight:${isWinner ? '700' : '400'};">
                        ${escHtml(c.cand_fullname)}
                        ${c.cand_partylist ? `<br><span style="font-size:0.75rem;font-weight:400;">(${escHtml(c.cand_partylist)})</span>` : ''}
                    </td>
                    <td style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.85rem;font-weight:${isWinner ? '700' : '400'};">${Number(c.vote_count).toLocaleString()}</td>
                    <td style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.85rem;">${pct}%</td>
                    <td style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;">
                        ${isWinner ? 'ELECTED' : ''}
                    </td>
                </tr>`;
            });

            return `
            <div style="margin-bottom:28px;page-break-inside:avoid;">
                <p style="margin:0 0 4px;font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#000;">Position</p>
                <h3 style="margin:0 0 8px;font-size:1rem;font-weight:800;color:#000;text-transform:uppercase;border-bottom:2px solid #000;padding-bottom:4px;">
                    ${escHtml(position)}
                </h3>
                <table style="width:100%;border-collapse:collapse;font-family:'Times New Roman',Times,serif;">
                    <thead>
                        <tr style="background:#000;color:#fff;">
                            <th style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;width:48px;">Rank</th>
                            <th style="padding:8px 12px;border:1px solid #000;text-align:left;font-size:0.8rem;font-weight:700;">Candidate Name</th>
                            <th style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;width:80px;">Votes</th>
                            <th style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;width:72px;">Percentage</th>
                            <th style="padding:8px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;width:72px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>${tableRows}</tbody>
                    <tfoot>
                        <tr style="background:#f0f0f0;">
                            <td colspan="2" style="padding:7px 12px;border:1px solid #000;font-size:0.8rem;font-weight:700;text-align:right;">Total Votes Cast:</td>
                            <td style="padding:7px 10px;border:1px solid #000;text-align:center;font-size:0.8rem;font-weight:700;">${totalVoters.toLocaleString()}</td>
                            <td colspan="2" style="border:1px solid #000;"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>`;
        }

        /* ── assemble document ── */
        const dateStr = new Date().toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
        const timeStr = new Date().toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' });

        let tablesHtml = '';
        Object.keys(groups).forEach(pos => { tablesHtml += buildTable(pos, groups[pos]); });

        const doc = `
        <div style="font-family:'Times New Roman',Times,serif;padding:48px 52px;color:#000;background:#fff;max-width:780px;margin:0 auto;">

            <!-- ── Document Header ── -->
            <div style="text-align:center;margin-bottom:32px;border-bottom:3px double #000;padding-bottom:20px;">
                <p style="margin:0 0 2px;font-size:0.82rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;">
                    Information Communication Club
                </p>
                <p style="margin:0 0 10px;font-size:0.78rem;letter-spacing:0.04em;">
                    Palawan Technological College, Inc.
                </p>
                <h1 style="margin:0 0 6px;font-size:1.55rem;font-weight:900;letter-spacing:0.04em;text-transform:uppercase;">
                    Election Results
                </h1>
                <p style="margin:0;font-size:0.82rem;color:#000;">
                    Date Generated: ${dateStr} &nbsp;&nbsp;|&nbsp;&nbsp; Time: ${timeStr}
                </p>
            </div>

            <!-- ── Per-position Tables ── -->
            ${tablesHtml}

            <!-- ── Signature Block ── -->
            <div style="margin-top:40px;">
                <p style="font-size:0.8rem;margin:0 0 32px;font-style:italic;">
                    This document certifies that the above results are true and accurate as of the date generated.
                </p>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="width:30%;text-align:center;padding:0 10px;">
                            <div style="height:60px;"></div>
                            <div style="border-top:1px solid #000;padding-top:6px;font-size:0.8rem;font-weight:700;">Stelcom</div>
                            <div style="font-size:0.75rem;margin-top:2px;">Signature over Printed Name</div>
                        </td>
                        <td style="width:5%;"></td>
                        <td style="width:30%;text-align:center;padding:0 10px;">
                            <div style="height:60px;"></div>
                            <div style="border-top:1px solid #000;padding-top:6px;font-size:0.8rem;font-weight:700;">Authorized Signatory</div>
                            <div style="font-size:0.75rem;margin-top:2px;">Signature over Printed Name</div>
                        </td>
                        <td style="width:5%;"></td>
                        <td style="width:30%;text-align:center;padding:0 10px;">
                            <div style="height:60px;"></div>
                            <div style="border-top:1px solid #000;padding-top:6px;font-size:0.8rem;font-weight:700;">Student Council Adviser</div>
                            <div style="font-size:0.75rem;margin-top:2px;">Signature over Printed Name</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ── Footer ── -->
            <div style="margin-top:28px;padding-top:10px;border-top:1px solid #000;text-align:center;font-size:0.72rem;color:#000;">
                This is a system-generated document. &nbsp;|&nbsp; ${dateStr}
            </div>
        </div>`;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = doc;
        document.body.appendChild(wrapper);

        const opt = {
            margin:      0.4,
            filename:    `election-results-${new Date().toISOString().slice(0,10)}.pdf`,
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true, backgroundColor: '#ffffff' },
            jsPDF:       { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(wrapper).save().then(() => {
            document.body.removeChild(wrapper);
        });
    }
    </script>

</div>

<style>
.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 0 4px;
}

.results-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.export-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 0.875rem;
    font-weight: 500;
    background-color: var(--primary, #4f46e5);
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: opacity 0.2s ease;
    white-space: nowrap;
}

.export-btn:hover { opacity: 0.88; }
.export-btn i     { font-size: 0.85rem; }
</style>