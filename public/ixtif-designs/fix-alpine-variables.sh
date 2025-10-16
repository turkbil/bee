#!/bin/bash

# Menu-1: megaOpen, searchOpen, activeCategory
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, megaOpen: false, searchOpen: false, activeCategory: '\''electronics'\'' }"/g' design-menu-1.html

# Menu-4: activeTab
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, activeTab: '\''featured'\'' }"/g' design-menu-4.html

# Menu-5: isSticky, quickMenuOpen
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, isSticky: false, quickMenuOpen: false }"/g' design-menu-5.html

# Menu-7: hoverCategory
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, hoverCategory: null }"/g' design-menu-7.html

# Menu-8: gridOpen
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, gridOpen: false }"/g' design-menu-8.html

# Menu-9: searchFocused
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, searchFocused: false }"/g' design-menu-9.html

# Menu-10: openDropdown, openSubDropdown
sed -i '' 's/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"/x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null, openDropdown: null, openSubDropdown: null }"/g' design-menu-10.html

echo "✅ Tüm Alpine.js değişkenleri düzeltildi!"
