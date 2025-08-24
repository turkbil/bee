<div id="tetris-container" class="relative w-full h-full flex items-center justify-center focus:outline-none" tabindex="0">
    <!-- Game Container -->
    <div class="tetris-game relative flex flex-col items-center">
        <!-- Score and Info Panel -->
        <div class="text-white text-center mb-4">
            <h3 class="text-xl font-bold mb-2">{{ __('front.tetris') }}</h3>
            <div class="flex justify-center gap-6 text-sm">
                <div>
                    <span class="opacity-75">{{ __('front.score') }}:</span>
                    <span id="tetris-score" class="font-bold ml-1">0</span>
                </div>
                <div>
                    <span class="opacity-75">{{ __('front.level') }}:</span>
                    <span id="tetris-level" class="font-bold ml-1">1</span>
                </div>
                <div>
                    <span class="opacity-75">{{ __('front.lines') }}:</span>
                    <span id="tetris-lines" class="font-bold ml-1">0</span>
                </div>
            </div>
        </div>

        <!-- Game Board with Next Piece -->
        <div class="flex items-start gap-4">
            <!-- Game Board -->
            <div class="game-board-wrapper relative bg-black/30 rounded-xl p-3 backdrop-blur-sm shadow-2xl">
                <canvas id="tetris-board" width="300" height="600" class="border-2 border-white/40 rounded-lg"></canvas>
            
                <!-- Game Over Overlay -->
                <div id="game-over" class="absolute inset-0 bg-black/80 rounded-lg items-center justify-center hidden">
                    <div class="text-center text-white">
                        <h4 class="text-2xl font-bold mb-2">{{ __('front.game_over') }}</h4>
                        <p class="text-lg mb-4">{{ __('front.score') }}: <span id="final-score">0</span></p>
                        <button onclick="restartTetris()" class="px-6 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                            {{ __('front.play_again') }}
                        </button>
                    </div>
                </div>

                <!-- Pause Overlay -->
                <div id="game-paused" class="absolute inset-0 bg-black/60 rounded-lg items-center justify-center hidden">
                    <div class="text-center text-white">
                        <h4 class="text-xl font-bold mb-2">{{ __('front.game_paused') }}</h4>
                        <p class="text-sm opacity-75">{{ __('front.press_enter_to_continue') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Next Piece Preview -->
            <div>
                <div class="text-white text-sm mb-2 opacity-90 font-semibold text-center">{{ __('front.next') }}:</div>
                <canvas id="next-piece" width="100" height="100" class="bg-black/30 border-2 border-white/30 rounded-lg shadow-lg"></canvas>
            </div>
        </div>

        <!-- Controls -->
        <div class="mt-6 text-center text-white/60 text-xs">
            <div class="mb-3 font-semibold text-white/80">{{ __('front.game_controls') }}:</div>
            <div class="inline-block mx-auto">
                <!-- Tablo benzeri düzen -->
                <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-left">
                    <!-- Sol kolon - Hareket -->
                    <div class="min-w-[140px]">
                        <div class="text-white/80 font-semibold mb-2 text-center border-b border-white/20 pb-1">{{ __('front.movement') }}</div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">↑ / W / I / 8</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.rotate') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">↓ / S / K / 2</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.soft_drop') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">← / A / J / 4</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.move_left') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">→ / D / L / 6</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.move_right') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sağ kolon - Özel -->
                    <div class="min-w-[140px]">
                        <div class="text-white/80 font-semibold mb-2 text-center border-b border-white/20 pb-1">{{ __('front.special') }}</div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">Space / C</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.hard_drop') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">Enter / P</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.pause') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-white/90">R / T</span>
                                <span class="text-white/60">→</span>
                                <span class="text-white/90">{{ __('front.restart') }}</span>
                            </div>
                            <div class="flex justify-center items-center py-0.5 mt-2">
                                <span class="opacity-60 text-xs text-white/50">Mac {{ __('front.friendly_keys') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tetris-game {
        transform: scale(0.95);
        transform-origin: center;
    }

    #game-over.show,
    #game-paused.show {
        display: flex;
    }

    /* Animation for cleared lines */
    @keyframes line-clear {
        0% { opacity: 1; transform: scaleX(1); }
        50% { opacity: 0.5; transform: scaleX(1.2); }
        100% { opacity: 0; transform: scaleX(0); }
    }

    .line-clearing {
        animation: line-clear 0.5s ease-out;
    }

    /* Tetris game glow effect */
    #tetris-board {
        box-shadow: 0 0 30px rgba(147, 51, 234, 0.5), 0 0 60px rgba(59, 130, 246, 0.3);
    }
</style>

<script>
    // Tetris game implementation
    const BOARD_WIDTH = 10;
    const BOARD_HEIGHT = 20;
    const BLOCK_SIZE = 30;
    
    // Tetris pieces
    const PIECES = {
        I: {
            blocks: [[0,0,0,0], [1,1,1,1], [0,0,0,0], [0,0,0,0]],
            gradient: ['#87CEEB', '#4682B4'],
            shadow: '#2F4F4F'
        },
        O: {
            blocks: [[1,1], [1,1]],
            gradient: ['#F0E68C', '#DAA520'],
            shadow: '#B8860B'
        },
        T: {
            blocks: [[0,1,0], [1,1,1], [0,0,0]],
            gradient: ['#DDA0DD', '#9370DB'],
            shadow: '#663399'
        },
        S: {
            blocks: [[0,1,1], [1,1,0], [0,0,0]],
            gradient: ['#98FB98', '#32CD32'],
            shadow: '#228B22'
        },
        Z: {
            blocks: [[1,1,0], [0,1,1], [0,0,0]],
            gradient: ['#FFA07A', '#FF6347'],
            shadow: '#CD5C5C'
        },
        J: {
            blocks: [[1,0,0], [1,1,1], [0,0,0]],
            gradient: ['#ADD8E6', '#1E90FF'],
            shadow: '#0000CD'
        },
        L: {
            blocks: [[0,0,1], [1,1,1], [0,0,0]],
            gradient: ['#FFAB91', '#FF7043'],
            shadow: '#D84315'
        }
    };

    class TetrisGame {
        constructor() {
            this.canvas = document.getElementById('tetris-board');
            this.ctx = this.canvas.getContext('2d');
            this.nextCanvas = document.getElementById('next-piece');
            this.nextCtx = this.nextCanvas.getContext('2d');
            
            this.board = Array(BOARD_HEIGHT).fill().map(() => Array(BOARD_WIDTH).fill(0));
            this.currentPiece = null;
            this.nextPiece = null;
            this.pieceX = 0;
            this.pieceY = 0;
            
            this.score = 0;
            this.lines = 0;
            this.level = 1;
            this.dropInterval = 1000;
            this.lastDrop = 0;
            
            this.gameOver = false;
            this.paused = false;
            
            this.pieceTypes = Object.keys(PIECES);
            
            // Key repeat handling
            this.keys = {};
            this.keyRepeatDelay = 120; // Çok daha hızlı başlangıç (170'den 120'ye)
            this.keyRepeatInterval = 30; // Çok daha hızlı tekrar (50'den 30'a)
            this.keyTimers = {};
            this.lastMoveTime = 0;
            this.moveDelay = 0; // Delay after piece locks
            
            // Ghost piece and extended placement
            this.ghostY = 0;
            this.placementTimer = null;
            this.placementDelay = 500; // 0.5 saniye ek süre
            this.canExtendPlacement = true;
            
            this.init();
        }

        init() {
            this.spawnPiece();
            this.setupControls();
            this.gameLoop();
        }

        spawnPiece() {
            if (!this.nextPiece) {
                this.nextPiece = this.getRandomPiece();
            }
            
            this.currentPiece = this.nextPiece;
            this.nextPiece = this.getRandomPiece();
            
            this.pieceX = Math.floor((BOARD_WIDTH - this.currentPiece.blocks[0].length) / 2);
            this.pieceY = 0;
            
            // Check game over
            if (this.checkCollision()) {
                this.gameOver = true;
                document.getElementById('game-over').classList.add('show');
                document.getElementById('final-score').textContent = this.score;
            }
            
            this.drawNextPiece();
            this.calculateGhostPosition();
        }
        
        calculateGhostPosition() {
            if (!this.currentPiece) {
                this.ghostY = 0;
                return;
            }
            
            this.ghostY = this.pieceY;
            let maxIterations = BOARD_HEIGHT;
            let iterations = 0;
            
            while (!this.checkCollision(0, this.ghostY - this.pieceY + 1) && iterations < maxIterations) {
                this.ghostY++;
                iterations++;
            }
            
            // Güvenlik kontrolü
            if (this.ghostY >= BOARD_HEIGHT) {
                this.ghostY = BOARD_HEIGHT - 1;
            }
        }

        getRandomPiece() {
            const type = this.pieceTypes[Math.floor(Math.random() * this.pieceTypes.length)];
            return {
                type: type,
                blocks: JSON.parse(JSON.stringify(PIECES[type].blocks)),
                gradient: PIECES[type].gradient,
                shadow: PIECES[type].shadow
            };
        }

        rotatePiece() {
            const rotated = this.currentPiece.blocks[0].map((_, index) =>
                this.currentPiece.blocks.map(row => row[index]).reverse()
            );
            
            const originalBlocks = this.currentPiece.blocks;
            const originalX = this.pieceX;
            const originalY = this.pieceY;
            this.currentPiece.blocks = rotated;
            
            // Wall kick testleri - sırayla dene
            const wallKicks = [
                [0, 0],   // Normal pozisyon
                [-1, 0],  // 1 sola
                [1, 0],   // 1 sağa
                [-2, 0],  // 2 sola (I parçası için)
                [2, 0],   // 2 sağa (I parçası için)
                [0, -1],  // 1 yukarı
                [-1, -1], // 1 sola 1 yukarı
                [1, -1],  // 1 sağa 1 yukarı
            ];
            
            let rotationSuccessful = false;
            
            for (const [dx, dy] of wallKicks) {
                this.pieceX = originalX + dx;
                this.pieceY = originalY + dy;
                
                if (!this.checkCollision()) {
                    rotationSuccessful = true;
                    break;
                }
            }
            
            if (!rotationSuccessful) {
                // Hiçbir wall kick çalışmadı, eski haline dön
                this.currentPiece.blocks = originalBlocks;
                this.pieceX = originalX;
                this.pieceY = originalY;
            } else {
                this.calculateGhostPosition();
                
                // Döndürme sonrası yere değip değmediğini kontrol et
                if (this.checkCollision(0, 1)) {
                    this.extendPlacementTime();
                } else {
                    // Havadaysa placement timer'ı temizle
                    if (this.placementTimer) {
                        clearTimeout(this.placementTimer);
                        this.placementTimer = null;
                        this.canExtendPlacement = true;
                    }
                }
            }
        }

        checkCollision(dx = 0, dy = 0) {
            for (let y = 0; y < this.currentPiece.blocks.length; y++) {
                for (let x = 0; x < this.currentPiece.blocks[y].length; x++) {
                    if (this.currentPiece.blocks[y][x]) {
                        const newX = this.pieceX + x + dx;
                        const newY = this.pieceY + y + dy;
                        
                        if (newX < 0 || newX >= BOARD_WIDTH || 
                            newY >= BOARD_HEIGHT ||
                            (newY >= 0 && this.board[newY][newX])) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        movePiece(dx, dy) {
            if (!this.checkCollision(dx, dy)) {
                this.pieceX += dx;
                this.pieceY += dy;
                this.calculateGhostPosition();
                
                // Yan hareket sonrası yere değip değmediğini kontrol et
                if (dx !== 0) {
                    // Eğer aşağı hareket edemiyorsa (yerdeyse)
                    if (this.checkCollision(0, 1)) {
                        this.extendPlacementTime();
                    } else {
                        // Havadaysa placement timer'ı temizle
                        if (this.placementTimer) {
                            clearTimeout(this.placementTimer);
                            this.placementTimer = null;
                            this.canExtendPlacement = true;
                        }
                    }
                }
                
                return true;
            }
            return false;
        }

        dropPiece() {
            if (!this.movePiece(0, 1)) {
                // Placement timer başlat (eğer daha önce başlamamışsa)
                if (!this.placementTimer && this.canExtendPlacement) {
                    this.startPlacementTimer();
                    return true; // Henüz kilitleme, placement süresini ver
                } else if (this.placementTimer) {
                    return true; // Placement timer aktif, henüz kilitleme
                } else {
                    // Timer dolmuş veya hiç başlamamış, kilitlensin
                    this.forceLock();
                    return false;
                }
            } else {
                // Parça hareket edebildi, placement timer'ı temizle
                if (this.placementTimer) {
                    clearTimeout(this.placementTimer);
                    this.placementTimer = null;
                    this.canExtendPlacement = true;
                }
            }
            return true;
        }
        
        startPlacementTimer() {
            // Önce eski timer'ı temizle
            if (this.placementTimer) {
                clearTimeout(this.placementTimer);
            }
            
            this.placementTimer = setTimeout(() => {
                // Timer tetiklendiğinde parça hala aşağı hareket edemiyorsa kilitle
                if (this.currentPiece && this.checkCollision(0, 1)) {
                    this.forceLock();
                } else {
                    // Parça hareket edebiliyorsa timer'ı temizle
                    this.placementTimer = null;
                    this.canExtendPlacement = true;
                }
            }, this.placementDelay);
        }
        
        forceLock() {
            if (this.placementTimer) {
                clearTimeout(this.placementTimer);
                this.placementTimer = null;
            }
            this.canExtendPlacement = true;
            
            // Aktif tuşları kaydet (yeni parça için)
            const activeKeys = {};
            Object.keys(this.keys).forEach(key => {
                if (this.keys[key] && (key === 'ArrowLeft' || key === 'ArrowRight')) {
                    activeKeys[key] = true;
                }
            });
            
            this.lockPiece();
            this.clearLines();
            this.spawnPiece();
            
            // Tuşlar hala basılıysa yeni parça için timer'ları yeniden başlat
            Object.keys(activeKeys).forEach(key => {
                if (activeKeys[key]) {
                    this.restartKeyTimer(key);
                }
            });
        }
        
        extendPlacementTime() {
            if (this.placementTimer && this.canExtendPlacement) {
                clearTimeout(this.placementTimer);
                this.startPlacementTimer();
                this.canExtendPlacement = false; // Sadece bir kez uzatabilir
            }
        }
        
        restartKeyTimer(key) {
            // Eski timer'ı temizle
            if (this.keyTimers[key]) {
                clearTimeout(this.keyTimers[key].timeout);
                clearInterval(this.keyTimers[key].interval);
            }
            
            // Yeni parça için hareketi hemen başlat
            setTimeout(() => {
                if (this.keys[key]) { // Tuş hala basılı mı kontrol et
                    if (key === 'ArrowLeft') {
                        this.handleKeyRepeat('left', () => this.movePiece(-1, 0));
                    } else if (key === 'ArrowRight') {
                        this.handleKeyRepeat('right', () => this.movePiece(1, 0));
                    }
                }
            }, 50); // Çok kısa bekleme sonrası başla
        }

        hardDrop() {
            // Hard drop - hemen kilitlensin, timer olmasın
            while (this.movePiece(0, 1)) {
                this.score += 2;
            }
            
            // Hard drop sonrası direkt kilitle, timer başlatma
            this.forceLock();
            this.updateScore();
        }

        lockPiece() {
            // Placement timer'ı temizle
            if (this.placementTimer) {
                clearTimeout(this.placementTimer);
                this.placementTimer = null;
            }
            this.canExtendPlacement = true;
            
            for (let y = 0; y < this.currentPiece.blocks.length; y++) {
                for (let x = 0; x < this.currentPiece.blocks[y].length; x++) {
                    if (this.currentPiece.blocks[y][x]) {
                        const boardY = this.pieceY + y;
                        const boardX = this.pieceX + x;
                        if (boardY >= 0) {
                            this.board[boardY][boardX] = {
                                gradient: this.currentPiece.gradient,
                                shadow: this.currentPiece.shadow
                            };
                        }
                    }
                }
            }
            // Add delay after locking piece
            this.moveDelay = Date.now() + 50; // Daha kısa delay (100'den 50'ye)
        }

        clearLines() {
            let linesCleared = 0;
            
            for (let y = BOARD_HEIGHT - 1; y >= 0; y--) {
                if (this.board[y].every(cell => cell !== 0)) {
                    this.board.splice(y, 1);
                    this.board.unshift(Array(BOARD_WIDTH).fill(0));
                    linesCleared++;
                    y++; // Check same line again
                }
            }
            
            if (linesCleared > 0) {
                this.lines += linesCleared;
                this.score += linesCleared * 100 * this.level;
                this.level = Math.floor(this.lines / 10) + 1;
                this.dropInterval = Math.max(100, 1000 - (this.level - 1) * 100);
                this.updateScore();
            }
        }

        updateScore() {
            document.getElementById('tetris-score').textContent = this.score;
            document.getElementById('tetris-level').textContent = this.level;
            document.getElementById('tetris-lines').textContent = this.lines;
        }

        draw() {
            // Clear canvas
            this.ctx.fillStyle = '#111';
            this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
            
            // Draw grid
            this.ctx.strokeStyle = '#222';
            this.ctx.lineWidth = 0.5;
            for (let x = 0; x <= BOARD_WIDTH; x++) {
                this.ctx.beginPath();
                this.ctx.moveTo(x * BLOCK_SIZE, 0);
                this.ctx.lineTo(x * BLOCK_SIZE, this.canvas.height);
                this.ctx.stroke();
            }
            for (let y = 0; y <= BOARD_HEIGHT; y++) {
                this.ctx.beginPath();
                this.ctx.moveTo(0, y * BLOCK_SIZE);
                this.ctx.lineTo(this.canvas.width, y * BLOCK_SIZE);
                this.ctx.stroke();
            }
            
            // Draw board
            for (let y = 0; y < BOARD_HEIGHT; y++) {
                for (let x = 0; x < BOARD_WIDTH; x++) {
                    if (this.board[y][x]) {
                        this.drawBlock(x, y, this.board[y][x].gradient, this.board[y][x].shadow);
                    }
                }
            }
            
            // Draw ghost piece (gölge)
            if (this.currentPiece && this.ghostY > this.pieceY) {
                for (let y = 0; y < this.currentPiece.blocks.length; y++) {
                    for (let x = 0; x < this.currentPiece.blocks[y].length; x++) {
                        if (this.currentPiece.blocks[y][x]) {
                            this.drawGhostBlock(
                                this.pieceX + x, 
                                this.ghostY + y
                            );
                        }
                    }
                }
            }
            
            // Draw current piece
            if (this.currentPiece) {
                for (let y = 0; y < this.currentPiece.blocks.length; y++) {
                    for (let x = 0; x < this.currentPiece.blocks[y].length; x++) {
                        if (this.currentPiece.blocks[y][x]) {
                            this.drawBlock(
                                this.pieceX + x, 
                                this.pieceY + y, 
                                this.currentPiece.gradient,
                                this.currentPiece.shadow
                            );
                        }
                    }
                }
            }
        }

        drawBlock(x, y, gradientColors, shadowColor) {
            const padding = 2;
            const blockX = x * BLOCK_SIZE + padding;
            const blockY = y * BLOCK_SIZE + padding;
            const blockWidth = BLOCK_SIZE - padding * 2;
            const blockHeight = BLOCK_SIZE - padding * 2;
            
            // Draw shadow/border
            this.ctx.fillStyle = shadowColor;
            this.ctx.fillRect(blockX - 1, blockY - 1, blockWidth + 2, blockHeight + 2);
            
            // Create gradient for main block
            const blockGradient = this.ctx.createLinearGradient(
                blockX, blockY,
                blockX + blockWidth, blockY + blockHeight
            );
            blockGradient.addColorStop(0, gradientColors[0]);
            blockGradient.addColorStop(1, gradientColors[1]);
            
            // Draw main block with rounded corners
            this.ctx.fillStyle = blockGradient;
            this.roundedRect(this.ctx, blockX, blockY, blockWidth, blockHeight, 4);
            this.ctx.fill();
            
            // Add shiny gradient overlay
            const shineGradient = this.ctx.createLinearGradient(
                blockX, blockY,
                blockX + blockWidth, blockY + blockHeight
            );
            shineGradient.addColorStop(0, 'rgba(255,255,255,0.5)');
            shineGradient.addColorStop(0.5, 'rgba(255,255,255,0.25)');
            shineGradient.addColorStop(1, 'rgba(255,255,255,0.1)');
            this.ctx.fillStyle = shineGradient;
            this.roundedRect(this.ctx, blockX, blockY, blockWidth - 1, blockHeight - 1, 4);
            this.ctx.fill();
            
            // Add inner highlight
            this.ctx.strokeStyle = 'rgba(255,255,255,0.6)';
            this.ctx.lineWidth = 1;
            this.roundedRect(this.ctx, blockX + 2, blockY + 2, blockWidth - 4, blockHeight - 4, 3);
            this.ctx.stroke();
        }
        
        drawGhostBlock(x, y) {
            const padding = 4;
            const blockX = x * BLOCK_SIZE + padding;
            const blockY = y * BLOCK_SIZE + padding;
            const blockWidth = BLOCK_SIZE - padding * 2;
            const blockHeight = BLOCK_SIZE - padding * 2;
            
            // Ghost piece çok hafif kenar çizgisi
            this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
            this.ctx.lineWidth = 1;
            this.ctx.setLineDash([3, 3]); // Daha kısa kesikli çizgi
            this.roundedRect(this.ctx, blockX, blockY, blockWidth, blockHeight, 3);
            this.ctx.stroke();
            this.ctx.setLineDash([]); // Kesikli çizgiyi sıfırla
        }
        
        roundedRect(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        drawNextPiece() {
            this.nextCtx.fillStyle = '#0a0a0a';
            this.nextCtx.fillRect(0, 0, this.nextCanvas.width, this.nextCanvas.height);
            
            if (this.nextPiece) {
                const blockSize = 22;
                const offsetX = (this.nextCanvas.width - this.nextPiece.blocks[0].length * blockSize) / 2;
                const offsetY = (this.nextCanvas.height - this.nextPiece.blocks.length * blockSize) / 2;
                
                for (let y = 0; y < this.nextPiece.blocks.length; y++) {
                    for (let x = 0; x < this.nextPiece.blocks[y].length; x++) {
                        if (this.nextPiece.blocks[y][x]) {
                            // Draw shadow
                            this.nextCtx.fillStyle = this.nextPiece.shadow;
                            this.nextCtx.fillRect(
                                offsetX + x * blockSize,
                                offsetY + y * blockSize,
                                blockSize,
                                blockSize
                            );
                            
                            // Create gradient for main block
                            const blockGradient = this.nextCtx.createLinearGradient(
                                offsetX + x * blockSize,
                                offsetY + y * blockSize,
                                offsetX + x * blockSize + blockSize,
                                offsetY + y * blockSize + blockSize
                            );
                            blockGradient.addColorStop(0, this.nextPiece.gradient[0]);
                            blockGradient.addColorStop(1, this.nextPiece.gradient[1]);
                            
                            this.nextCtx.fillStyle = blockGradient;
                            this.roundedRectNext(
                                this.nextCtx,
                                offsetX + x * blockSize + 1,
                                offsetY + y * blockSize + 1,
                                blockSize - 2,
                                blockSize - 2,
                                3
                            );
                            this.nextCtx.fill();
                            
                            // Add shine
                            const gradient = this.nextCtx.createLinearGradient(
                                offsetX + x * blockSize,
                                offsetY + y * blockSize,
                                offsetX + x * blockSize + blockSize,
                                offsetY + y * blockSize + blockSize
                            );
                            gradient.addColorStop(0, 'rgba(255,255,255,0.3)');
                            gradient.addColorStop(1, 'rgba(255,255,255,0.1)');
                            this.nextCtx.fillStyle = gradient;
                            this.roundedRectNext(
                                this.nextCtx,
                                offsetX + x * blockSize + 1,
                                offsetY + y * blockSize + 1,
                                blockSize - 2,
                                blockSize - 2,
                                3
                            );
                            this.nextCtx.fill();
                        }
                    }
                }
            }
        }
        
        roundedRectNext(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        setupControls() {
            // Focus container on click
            const container = document.getElementById('tetris-container');
            container.addEventListener('click', () => {
                container.focus();
            });
            
            // Auto focus when game starts
            setTimeout(() => container.focus(), 100);
            
            container.addEventListener('keydown', (e) => {
                // Prevent default for all game-related keys - Genişletilmiş tuş listesi
                const gameKeys = [
                    'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', ' ', 'Enter', 
                    'r', 'R', 't', 'T', 'p', 'P', 'c', 'C',
                    'w', 'W', 'a', 'A', 's', 'S', 'd', 'D',
                    'j', 'J', 'k', 'K', 'l', 'L', 'i', 'I',
                    '2', '4', '6', '8'
                ];
                
                if (gameKeys.includes(e.key)) {
                    e.preventDefault();
                }
                
                if (this.gameOver) {
                    if (e.key === ' ' || e.key.toLowerCase() === 'r' || e.key.toLowerCase() === 't' || e.key.toLowerCase() === 'c') {
                        restartTetris();
                    }
                    return;
                }
                
                // Pause kontrolü - Enter ve P
                if (e.key === 'Enter' || e.key.toLowerCase() === 'p') {
                    this.togglePause();
                    return;
                }
                
                if (this.paused) return;
                
                // Check if we're still in move delay
                if (Date.now() < this.moveDelay) return;
                
                // Clear all key timers on any new key press
                if (!this.keys[e.key]) {
                    Object.keys(this.keyTimers).forEach(key => {
                        clearTimeout(this.keyTimers[key].timeout);
                        clearInterval(this.keyTimers[key].interval);
                        delete this.keyTimers[key];
                    });
                }
                
                this.keys[e.key] = true;
                
                switch(e.key) {
                    // Sol hareket - Arrow, A, J, 4
                    case 'ArrowLeft':
                    case 'a':
                    case 'A':
                    case 'j':
                    case 'J':
                    case '4':
                        this.handleKeyRepeat('left', () => this.movePiece(-1, 0));
                        break;
                    
                    // Sağ hareket - Arrow, D, L, 6
                    case 'ArrowRight':
                    case 'd':
                    case 'D':
                    case 'l':
                    case 'L':
                    case '6':
                        this.handleKeyRepeat('right', () => this.movePiece(1, 0));
                        break;
                    
                    // Aşağı hareket - Arrow, S, K, 2
                    case 'ArrowDown':
                    case 's':
                    case 'S':
                    case 'k':
                    case 'K':
                    case '2':
                        this.handleKeyRepeat('down', () => {
                            if (this.dropPiece()) {
                                this.score += 1;
                                this.updateScore();
                            }
                        });
                        break;
                    
                    // Döndürme - Arrow Up, W, I, 8
                    case 'ArrowUp':
                    case 'w':
                    case 'W':
                    case 'i':
                    case 'I':
                    case '8':
                        this.rotatePiece();
                        break;
                    
                    // Hard Drop - Space ve C
                    case ' ':
                    case 'c':
                    case 'C':
                        this.hardDrop();
                        break;
                    
                    // Restart - R ve T
                    case 'r':
                    case 'R':
                    case 't':
                    case 'T':
                        restartTetris();
                        break;
                }
            });
            
            container.addEventListener('keyup', (e) => {
                this.keys[e.key] = false;
                
                // Clear timers for this key
                if (this.keyTimers[e.key]) {
                    clearTimeout(this.keyTimers[e.key].timeout);
                    clearInterval(this.keyTimers[e.key].interval);
                    delete this.keyTimers[e.key];
                }
            });
        }
        
        handleKeyRepeat(key, action) {
            // Execute action immediately
            action();
            
            // Clear any existing timer for this key
            if (this.keyTimers[key]) {
                clearTimeout(this.keyTimers[key].timeout);
                clearInterval(this.keyTimers[key].interval);
            }
            
            // Set up repeat after delay
            this.keyTimers[key] = {
                timeout: setTimeout(() => {
                    this.keyTimers[key].interval = setInterval(() => {
                        if (this.keys['Arrow' + key.charAt(0).toUpperCase() + key.slice(1)]) {
                            action();
                        } else {
                            clearInterval(this.keyTimers[key].interval);
                            delete this.keyTimers[key];
                        }
                    }, this.keyRepeatInterval);
                }, this.keyRepeatDelay)
            };
        }

        togglePause() {
            this.paused = !this.paused;
            const pauseOverlay = document.getElementById('game-paused');
            if (this.paused) {
                pauseOverlay.classList.add('show');
            } else {
                pauseOverlay.classList.remove('show');
            }
        }

        gameLoop(timestamp = 0) {
            if (!this.gameOver) {
                if (!this.paused) {
                    if (timestamp - this.lastDrop > this.dropInterval) {
                        // Otomatik düşme - sadece placement timer yoksa
                        if (!this.placementTimer) {
                            this.dropPiece();
                        }
                        this.lastDrop = timestamp;
                    }
                    this.draw();
                }
                requestAnimationFrame((ts) => this.gameLoop(ts));
            }
        }
    }

    let tetrisGame = null;

    function startTetris() {
        if (!tetrisGame) {
            tetrisGame = new TetrisGame();
        }
    }

    function restartTetris() {
        document.getElementById('game-over').classList.remove('show');
        document.getElementById('game-paused').classList.remove('show');
        tetrisGame = new TetrisGame();
    }

    // Start game when component loads
    setTimeout(startTetris, 500);
</script>