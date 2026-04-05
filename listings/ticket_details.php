<?php 

session_start();

require_once '../config/database.php';

if (empty($_SESSION['scraped_ticket'])) {
    header("Location: ticket_listing.php");
    exit();
}

$scraped = $_SESSION['scraped_ticket'];

// We check if the ticket was automatically verified by our Fatsoma scraper.
$isVerified = ($scraped['is_verified'] === true);

// If the ticket is verified, we make the input fields read-only so the info cannot be faked.
$readonlyAttr = $isVerified
    ? "readonly style='background-color: #F1F5F9; cursor: not-allowed;'"
    : "";

// We pull the event info out of the session and set defaults if anything is missing.
$event_name   = $scraped['event_name']   ?? '';
$location     = $scraped['venue']        ?? '';
$event_date   = $scraped['event_date']   ?? '';
$retail_price = (float)($scraped['retail_price'] ?? 0);
$manual_msg   = $scraped['manual_msg']   ?? '';

// This is where we set the rules for the selling price to stop people overcharging.
// Students can sell for a small profit (40% max) or a big discount (50% min).
if ($retail_price > 0) {
    $max_allowed = round($retail_price * 1.40, 2);
    $min_allowed = round($retail_price * 0.50, 2);
    $default_val = $retail_price;
} else {
    // If the ticket was originally free, it has to stay free on our platform too.
    $max_allowed = 0.00;
    $min_allowed = 0.00;
    $default_val = 0.00;
}

include '../includes/header.php'; 
?>

<div class="min-h-screen bg-[#F5F8FA] font-sans text-[#0A192F] pb-24">
    <div class="max-w-4xl mx-auto px-6 mt-12">
        
        <div class="flex items-center justify-between mb-8">
            <a href="javascript:history.back()" class="flex items-center text-[#64748B] hover:text-[#0052FF] transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-xs font-black uppercase tracking-widest">Back</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#E2E8F0]"></div>
            </div>
            <div class="w-10"></div>
        </div>

        <?php if (!$isVerified && !empty($manual_msg)): ?>
        <div class="bg-amber-50 border-l-4 border-amber-400 text-amber-800 p-4 mb-6 shadow-sm rounded-xl">
            <p class="font-bold text-sm">Manual Entry Required</p>
            <p class="text-xs mt-1"><?php echo htmlspecialchars($manual_msg); ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <form id="listingForm" action="ticket_review.php" method="POST" class="p-12 space-y-10">
                
                <div class="border-b border-[#F1F5F9] pb-8">
                    <h2 class="text-3xl font-black tracking-tighter text-[#0A192F]">Ticket Details</h2>
                    <p class="text-sm text-[#64748B] font-medium mt-1 uppercase tracking-tight">Step 2: Price & Policy Validation</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Event Name</label>
                        <input type="text" name="event_name" required <?php echo $readonlyAttr; ?>
                            value="<?php echo htmlspecialchars($event_name); ?>" 
                            class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Location</label>
                        <input type="text" name="location" required <?php echo $readonlyAttr; ?>
                            value="<?php echo htmlspecialchars($location); ?>" 
                            class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Event Date</label>
                        <input type="date" name="event_date" required <?php echo $readonlyAttr; ?>
                            value="<?php echo !empty($event_date) ? date('Y-m-d', strtotime($event_date)) : ''; ?>" 
                            class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Category</label>
                        <select name="category" required class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold outline-none">
                            <option value="club">Club Night</option>
                            <option value="sports">Sports</option>
                            <option value="society">Society</option>
                            <option value="society">Academic and Careers</option>
                            <option value="society">Other</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-12 p-8 bg-[#F0F7FF] rounded-2xl border border-[#0052FF]/5 mt-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-[#0052FF]">Retail Price (£)</label>
                            <input type="number" step="0.01" name="retail_price" id="retail_price" required <?php echo $readonlyAttr; ?>
                                value="<?php echo number_format((float)$retail_price, 2, '.', ''); ?>" 
                                class="w-full p-4 bg-white border-2 border-[#E2E8F0] rounded-xl font-bold">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-[#0052FF]">Your Selling Price (£)</label>
                            <?php 
                                // We make sure the price displays correctly with two decimals like 0.00
                                $display_val = number_format((float)$default_val, 2, '.', '');
                                // If the ticket is verified as free, we lock the selling price so they cannot charge for it
                                $selling_lock = ($isVerified && $retail_price <= 0) ? "readonly style='background-color: #F1F5F9; cursor: not-allowed;'" : "";
                            ?>
                            <input type="number" step="0.01" name="selling_price" id="selling_price" required
                                value="<?php echo $display_val; ?>"
                                <?php echo $selling_lock; ?>
                                class="w-full p-4 bg-white border-2 border-[#0052FF] rounded-xl font-black text-[#0052FF] outline-none transition-all">
                            
                            <p id="price-status" class="text-[9px] font-bold text-[#64748B] mt-1 uppercase italic tracking-widest">
                                <?php if ($isVerified && $retail_price <= 0): ?>
                                    <span class="text-[#EF4444]">Fixed Price: This is a verified FREE ticket.</span>
                                <?php elseif ($isVerified): ?>
                                    Allowed: £<?php echo number_format($min_allowed, 2); ?> to £<?php echo number_format($max_allowed, 2); ?>
                                <?php else: ?>
                                    Enter a retail price to calculate limits.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-[#0052FF] text-white rounded-xl font-black uppercase tracking-[0.2em] text-xs hover:bg-[#0A192F] transition-all shadow-xl">
                    Continue to Review
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // This script initialises the icons on the page
    lucide.createIcons();

    const retailInput = document.getElementById('retail_price');
    const sellingInput = document.getElementById('selling_price');
    const statusText = document.getElementById('price-status');
    const form = document.getElementById('listingForm');

    // We pull the price limits from PHP so the JavaScript knows when to block a sale
    let maxLimit = <?php echo $max_allowed; ?>;
    let minLimit = <?php echo $min_allowed; ?>;
    const isVerified = <?php echo $isVerified ? 'true' : 'false'; ?>;

    function validatePrices() {
        // If the field is locked because it is a free ticket, we don't need to validate it
        if (sellingInput.hasAttribute('readonly')) return;

        const retailVal = parseFloat(retailInput.value) || 0;
        const sellingVal = parseFloat(sellingInput.value) || 0;

        // If the student is typing manually, we calculate the price limits on the fly
        if (!isVerified) {
            maxLimit = retailVal > 0 ? (retailVal * 1.40) : 9999;
            minLimit = retailVal > 0 ? (retailVal * 0.50) : 0;
            
            if (retailVal > 0) {
                statusText.innerText = "Allowed: £" + minLimit.toFixed(2) + " to £" + maxLimit.toFixed(2);
            } else {
                statusText.innerText = "Enter a retail price to calculate limits";
            }
        }

        // We show a red warning if the price is too high or too low based on our rules
        if (retailVal > 0 && (sellingVal > maxLimit || sellingVal < minLimit)) {
            const errorMsg = sellingVal > maxLimit ? "ABOVE 40% CAP" : "BELOW 50% FLOOR";
            statusText.innerText = " BLOCKED: " + errorMsg;
            statusText.style.color = "#EF4444";
            sellingInput.style.borderColor = "#EF4444";
        } else if (retailVal > 0) {
            statusText.innerText = "Status: Within Valid Range";
            statusText.style.color = "#10B981";
            sellingInput.style.borderColor = "#0052FF";
        }
    }

    // We stop the form from being sent if the price breaks our fair-trade policy
    form.onsubmit = function(e) {
        const retailVal = parseFloat(retailInput.value) || 0;
        const sellingVal = parseFloat(sellingInput.value) || 0;

        if (retailVal > 0 && (sellingVal > maxLimit || sellingVal < minLimit)) {
            alert("Price violation: Your price must be between 50% and 140% of the retail price.");
            e.preventDefault();
            return false;
        }
    };

    // We listen for any changes in the price boxes to update the feedback live
    retailInput.addEventListener('input', validatePrices);
    sellingInput.addEventListener('input', validatePrices);
</script>

<?php include '../includes/footer.php'; ?>