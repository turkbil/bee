#!/bin/bash

##############################################################################
# AI Chat Widget - Comprehensive Test Suite
# Date: 2025-10-18
# Purpose: Test voltage, battery types, multi-term queries
##############################################################################

API_URL="https://ixtif.com/api/ai/v1/shop-assistant/chat"
RESULTS_DIR="/tmp/ai-chat-test-results"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
RESULTS_FILE="${RESULTS_DIR}/test_results_${TIMESTAMP}.txt"

# Create results directory
mkdir -p "${RESULTS_DIR}"

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test counter
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

##############################################################################
# Helper Functions
##############################################################################

print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_test() {
    echo -e "${YELLOW}TEST $1:${NC} $2"
}

print_success() {
    echo -e "${GREEN}✅ PASSED${NC}"
    ((PASSED_TESTS++))
}

print_failure() {
    echo -e "${RED}❌ FAILED${NC}"
    echo -e "${RED}Expected: $1${NC}"
    echo -e "${RED}Got: $2${NC}"
    ((FAILED_TESTS++))
}

run_test() {
    local test_name="$1"
    local query="$2"
    local expected_keyword="$3"

    ((TOTAL_TESTS++))

    print_test "${TOTAL_TESTS}" "${query}"

    # Run API request
    response=$(curl -s -X POST "${API_URL}" \
        -H "Content-Type: application/json" \
        -d "{\"message\":\"${query}\"}" 2>/dev/null)

    if [ $? -ne 0 ]; then
        print_failure "API response" "Connection error"
        echo -e "Query: ${query}\nError: Connection failed\n" >> "${RESULTS_FILE}"
        return 1
    fi

    # Extract message
    message=$(echo "${response}" | python3 -c "import sys, json; data=json.load(sys.stdin); print(data.get('data', {}).get('message', ''))" 2>/dev/null)

    if [ $? -ne 0 ]; then
        print_failure "JSON parsing" "Invalid JSON"
        echo -e "Query: ${query}\nError: Invalid JSON response\n" >> "${RESULTS_FILE}"
        return 1
    fi

    # Check if expected keyword is in response
    if echo "${message}" | grep -qi "${expected_keyword}"; then
        print_success
        echo -e "✅ ${test_name}\nQuery: ${query}\nFound: ${expected_keyword}\n" >> "${RESULTS_FILE}"
        echo -e "Response: ${message:0:200}...\n" >> "${RESULTS_FILE}"
        return 0
    else
        print_failure "${expected_keyword}" "Keyword not found in response"
        echo -e "❌ ${test_name}\nQuery: ${query}\nExpected: ${expected_keyword}\nResponse: ${message:0:500}\n" >> "${RESULTS_FILE}"
        return 1
    fi
}

##############################################################################
# TEST SUITE 1: VOLTAGE SPECIFICATIONS
##############################################################################

print_header "TEST SUITE 1: VOLTAGE SPECIFICATIONS"

run_test "12V Battery" "12V bataryalı transpalet var mı" "12V"
run_test "24V Battery" "24V bataryalı transpalet" "24V"
run_test "36V Battery" "36V bataryalı forklift" "36V"
run_test "48V Battery" "48V bataryalı transpalet modelleri" "48V"
run_test "80V Battery" "80V bataryalı istifleyici" "80V"

# Variations
run_test "Voltage with unit" "48 volt transpalet" "48"
run_test "Voltage lowercase" "24v bataryalı" "24"

##############################################################################
# TEST SUITE 2: BATTERY TYPES
##############################################################################

print_header "TEST SUITE 2: BATTERY TYPES"

run_test "Li-Ion Battery" "Li-Ion bataryalı transpalet" "Li-Ion"
run_test "AGM Battery" "AGM bataryalı transpalet" "AGM"
run_test "Lithium Battery" "Lithium bataryalı forklift" "lithium"
run_test "Electric Battery" "elektrikli transpalet" "elektrik"

# Battery capacity queries
run_test "Battery Capacity 1" "yüksek kapasiteli batarya" "batarya"
run_test "Battery Capacity 2" "uzun ömürlü batarya" "batarya"

##############################################################################
# TEST SUITE 3: CAPACITY SPECIFICATIONS
##############################################################################

print_header "TEST SUITE 3: CAPACITY SPECIFICATIONS"

run_test "1.5 Ton" "1.5 ton transpalet" "1.5"
run_test "2.0 Ton" "2 ton transpalet" "2"
run_test "2.5 Ton" "2.5 ton kapasiteli" "2.5"
run_test "3.0 Ton" "3 ton forklift" "3"

# Capacity in kg
run_test "1500 kg" "1500 kg transpalet" "1500\\|1.5"
run_test "2000 kg" "2000 kg kapasiteli" "2000\\|2"

##############################################################################
# TEST SUITE 4: SPECIAL FEATURES
##############################################################################

print_header "TEST SUITE 4: SPECIAL FEATURES"

run_test "Cold Storage" "soğuk depo transpalet" "soğuk\\|ETC\\|cold"
run_test "Narrow Aisle" "dar koridor forklift" "dar\\|koridor"
run_test "Stainless Steel" "paslanmaz transpalet" "paslanmaz\\|stainless"
run_test "Scale Equipped" "terazili transpalet" "terazi\\|tartı"
run_test "Autonomous" "otonom transpalet" "otonom\\|AGV"

##############################################################################
# TEST SUITE 5: MULTI-TERM QUERIES (Complex)
##############################################################################

print_header "TEST SUITE 5: MULTI-TERM COMPLEX QUERIES"

run_test "Complex 1" "48V Li-Ion 2 ton transpalet" "48V\\|Li-Ion\\|2"
run_test "Complex 2" "soğuk depo için 1.5 ton elektrikli" "soğuk\\|1.5\\|elektrik"
run_test "Complex 3" "24V AGM bataryalı 2 ton" "24V\\|AGM\\|2"
run_test "Complex 4" "Li-Ion bataryalı dar koridor reach truck" "Li-Ion\\|dar\\|reach"
run_test "Complex 5" "paslanmaz çelik gıda sektörü transpalet" "paslanmaz\\|gıda"

##############################################################################
# TEST SUITE 6: LIFT HEIGHT
##############################################################################

print_header "TEST SUITE 6: LIFT HEIGHT SPECIFICATIONS"

run_test "3 meter lift" "3 metre kaldırma yüksekliği" "3\\|metre\\|kaldırma"
run_test "5 meter lift" "5 metre yüksekliğe kaldıran" "5\\|metre"
run_test "High lift" "yüksek kaldırma" "kaldırma\\|yüksek"

##############################################################################
# TEST SUITE 7: DRIVE TYPE
##############################################################################

print_header "TEST SUITE 7: DRIVE TYPE"

run_test "Manual" "manuel transpalet" "manuel"
run_test "Electric" "elektrikli transpalet" "elektrik"
run_test "LPG" "LPG'li forklift" "LPG"
run_test "Diesel" "dizel forklift" "dizel"

##############################################################################
# TEST SUITE 8: USE CASE SCENARIOS
##############################################################################

print_header "TEST SUITE 8: USE CASE SCENARIOS"

run_test "Long Shift" "uzun vardiya için transpalet" "vardiya\\|batarya"
run_test "Outdoor Use" "dış mekan kullanım" "dış\\|outdoor"
run_test "Indoor Use" "kapalı alan transpalet" "kapalı\\|indoor"
run_test "Food Industry" "gıda sektörü için" "gıda\\|paslanmaz"
run_test "Warehouse" "depo kullanımı" "depo"

##############################################################################
# TEST SUITE 9: BRAND/MODEL SPECIFIC
##############################################################################

print_header "TEST SUITE 9: BRAND/MODEL QUERIES"

run_test "IXTIF Brand" "İXTİF transpalet modelleri" "İXTİF\\|IXTIF"
run_test "EPT Series" "EPT serisi transpalet" "EPT"
run_test "EPL Series" "EPL serisi Li-Ion" "EPL"
run_test "Model Number" "EPT20 modeli" "EPT20"

##############################################################################
# TEST SUITE 10: NEGATIVE TESTS (Should handle gracefully)
##############################################################################

print_header "TEST SUITE 10: NEGATIVE TESTS"

# These should NOT crash, should give helpful response
run_test "Non-existent voltage" "1000V transpalet" "transpalet\\|modelimiz"
run_test "Unrealistic capacity" "100 ton transpalet" "transpalet"
run_test "Gibberish query" "asdfghjkl transpalet" "yardımcı\\|detay"

##############################################################################
# RESULTS SUMMARY
##############################################################################

print_header "TEST RESULTS SUMMARY"

echo -e "${BLUE}Total Tests:${NC} ${TOTAL_TESTS}"
echo -e "${GREEN}Passed:${NC} ${PASSED_TESTS}"
echo -e "${RED}Failed:${NC} ${FAILED_TESTS}"

SUCCESS_RATE=$(echo "scale=2; ${PASSED_TESTS} * 100 / ${TOTAL_TESTS}" | bc)
echo -e "${YELLOW}Success Rate:${NC} ${SUCCESS_RATE}%"

echo -e "\n${BLUE}Detailed results saved to:${NC} ${RESULTS_FILE}"

# Summary to results file
echo -e "\n========================================" >> "${RESULTS_FILE}"
echo -e "SUMMARY" >> "${RESULTS_FILE}"
echo -e "========================================" >> "${RESULTS_FILE}"
echo -e "Total Tests: ${TOTAL_TESTS}" >> "${RESULTS_FILE}"
echo -e "Passed: ${PASSED_TESTS}" >> "${RESULTS_FILE}"
echo -e "Failed: ${FAILED_TESTS}" >> "${RESULTS_FILE}"
echo -e "Success Rate: ${SUCCESS_RATE}%" >> "${RESULTS_FILE}"
echo -e "Timestamp: $(date)" >> "${RESULTS_FILE}"

# Exit code
if [ ${FAILED_TESTS} -eq 0 ]; then
    echo -e "\n${GREEN}🎉 ALL TESTS PASSED!${NC}\n"
    exit 0
else
    echo -e "\n${RED}⚠️  SOME TESTS FAILED${NC}\n"
    exit 1
fi
