<?php
// Mock data for the static layout
$events = [
    [
        'title' => 'The Winter Ball 2026',
        'date' => 'Friday, 20th Feb',
        'venue' => 'Main Students Union',
        'status' => 'Sold Out',
        'resale_count' => 12,
        'image' => 'https://images.unsplash.com/photo-1514525253344-7633979148d8?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Sports Night: Neon Glow',
        'date' => 'Wednesday, 11th Feb',
        'venue' => 'The Warehouse Club',
        'status' => 'Available',
        'resale_count' => 4,
        'image' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Post-Grad Gala',
        'date' => 'Saturday, 14th Feb',
        'venue' => 'Grand Hall',
        'status' => 'Sold Out',
        'resale_count' => 2,
        'image' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80'
    ]
];

include '../includes/header.php'; 
?>

<div class="mx-auto px-6 lg:px-[60px] py-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-[#0A192F] mb-3">Event Hub</h1>
            <p class="text-[#64748B] font-medium">Browse official events and find verified student resales.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="text-sm font-bold text-[#64748B] mr-2">Sort by:</span>
            <select class="bg-white border border-[#E2E8F0] rounded-lg px-4 py-2 text-sm font-bold focus:outline-none focus:border-[#0052FF]">
                <option>Date (Soonest)</option>
                <option>Most Popular</option>
                <option>Sold Out First</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($events as $event): ?>
            <div class="group bg-white rounded-2xl border border-[#E2E8F0] overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo $event['image']; ?>" alt="Event" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    <div class="absolute top-4 left-4">
                        <?php if ($event['status'] === 'Sold Out'): ?>
                            <span class="bg-red-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                                Sold Out Officially
                            </span>
                        <?php else: ?>
                            <span class="bg-green-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                                Tickets Available
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-[#0052FF] uppercase tracking-widest"><?php echo $event['date']; ?></span>
                        <div class="flex items-center text-[#64748B]">
                             <span class="text-xs font-bold"><?php echo $event['venue']; ?></span>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-[#0A192F] mb-4"><?php echo $event['title']; ?></h3>

                    <div class="flex items-center justify-between pt-4 border-t border-[#F1F5F9]">
                        <div>
                            <p class="text-[10px] font-bold text-[#64748B] uppercase">Student Sellers</p>
                            <p class="text-lg font-black text-[#0A192F]"><?php echo $event['resale_count']; ?> Available</p>
                        </div>
                        <a hqref="/student2student/events/view-event.php?id=1" class="bg-[#F4F7FA] hover:bg-[#0052FF] hover:text-white text-[#0A192F] px-5 py-2.5 rounded-xl text-sm font-bold transition-all">
                            View Tickets
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>