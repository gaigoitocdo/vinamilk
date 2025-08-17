// /assets/js/progress.js
const baseline = [1, 1, 1, 1];

(() => {
  const ROUND_SEC = 180,
        STEP_SEC  = 8,
        PRIME     = 97;

  const rngFactory = seed => () => {
    seed |= 0; seed = (seed + 0x6D2B79F5)|0;
    let t = Math.imul(seed ^ seed>>>15, 1|seed);
    t = (t + Math.imul(t ^ t>>>7, 61|t)) ^ t;
    return ((t ^ t>>>14)>>>0) / 4294967296;
  };

  let fract = [0.25, 0.25, 0.25, 0.25];

  window.initProgressSim = sessionId => {
    const MIN_LEAD = 0.4;
    const MAX_LEAD = 0.6;

    const params = new URLSearchParams(location.search);
    const rk   = parseInt(params.get('rank') || window.RANK_KEY || '1', 10);
    const seed = Number(sessionId) * PRIME + rk;
    const rng  = rngFactory(seed);

    const leaderIndex = Math.floor(rng() * 4);
    const leadPercent = MIN_LEAD + rng() * (MAX_LEAD - MIN_LEAD);

    const remaining = 1 - leadPercent;
    let r1 = rng() * remaining;
    let r2 = rng() * (remaining - r1);
    let r3 = remaining - r1 - r2;

    let temp = [r1, r2, r3];

    fract = [0, 0, 0, 0];
    fract[leaderIndex] = leadPercent;

    let j = 0;
    for (let i = 0; i < 4; i++) {
      if (i !== leaderIndex) {
        fract[i] = temp[j++];
      }
    }

    drawProgressBars(ROUND_SEC);
  };

  window.drawProgressBars = remainSec => {
    const elapsed    = ROUND_SEC - remainSec;
    const ticks      = Math.floor(elapsed / STEP_SEC);
    const maxSeconds = ROUND_SEC;

    const rawPpl = fract.map(f => Math.floor(f * ticks * 0.5));
    const baselineProgress = baseline.map(b =>
      Math.floor(b * elapsed / maxSeconds)
    );

    const pplArr = rawPpl.map((n,i) =>
      n + baselineProgress[i] + (window.totalUnits[i] || 0)
    );

    const totalPpl = pplArr.reduce((a,b) => a+b, 0) || 1;

    for (let i = 0; i < 4; i++) {
      const pct = Math.round(pplArr[i] / totalPpl * 100);
      document.getElementById(`bar${i}`).style.width     = pct + '%';
      document.getElementById(`percent${i}`).textContent = pct + '%';
      document.getElementById(`people${i}`).textContent  = pplArr[i];
    }
  };
})();