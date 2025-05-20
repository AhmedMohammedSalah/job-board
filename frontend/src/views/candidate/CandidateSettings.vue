<template>
  <div class="candidate-settings">
    <!-- Main Form Section -->
    <div class="settings-card">
      <h2 class="settings-title">Profile Settings</h2>

      <!-- Personal Information -->
      <div class="settings-section">
        <h3>Personal Information</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Full Name</label>
            <input
              v-model="formData.user.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': v$.user.name.$error }"
            />
            <div v-if="v$.user.name.$error" class="invalid-feedback">
              Name is required
            </div>
          </div>

          <div class="form-group">
            <label>Email Address</label>
            <input
              v-model="formData.user.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': v$.user.email.$error }"
            />
            <div v-if="v$.user.email.$error" class="invalid-feedback">
              Valid email is required
            </div>
          </div>
        </div>
      </div>

      <!-- Professional Information -->
      <div class="settings-section">
        <h3>Professional Information</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Professional Headline</label>
            <input
              v-model="formData.headline"
              type="text"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>LinkedIn Profile</label>
            <input
              v-model="formData.linkedin_url"
              type="url"
              class="form-control"
              placeholder="https://linkedin.com/in/your-profile"
            />
          </div>

          <div class="form-group">
            <label>Years of Experience</label>
            <select v-model="formData.experience_years" class="form-control">
              <option v-for="n in 30" :key="n" :value="n">{{ n }} years</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Skills Section -->
      <div class="settings-section">
        <div class="section-header">
          <h3>Skills & Expertise</h3>
          <button class="btn btn-primary" @click="showAddSkillModal = true">
            Add Skill
          </button>
        </div>

        <div class="skills-container">
          <div
            v-for="skill in candidateSkills"
            :key="skill.id"
            class="skill-badge"
          >
            {{ getSkillName(skill.skill_id) }} ({{ skill.level }})
            <button class="remove-skill" @click="removeSkill(skill.id)">
              &times;
            </button>
          </div>
        </div>

        <!-- Skills Multiselect -->
        <div class="form-group">
          <label>Select Skills</label>
          <multiselect
            v-model="selectedSkills"
            :options="skillOptions"
            :multiple="true"
            :close-on-select="false"
            :limit="10"
            :show-labels="false"
            placeholder="Search and select skills"
            label="name"
            track-by="id"
            :searchable="true"
          />
        </div>
      </div>

      <!-- Resume Upload -->
      <!-- <div class="settings-section">
        <h3>Resume</h3>
        <file-upload
          v-model="formData.resume"
          :current-file="formData.resume_path"
          @update:modelValue="handleResumeUpload"
        />
      </div> -->

      <!-- Save Button -->
      <div class="form-actions">
        <button
          class="btn btn-primary"
          :disabled="loading"
          @click="saveProfile"
        >
          {{ loading ? "Saving..." : "Save Changes" }}
        </button>
      </div>
    </div>

    <!-- Add Skill Modal -->
    <modal v-if="showAddSkillModal" @close="showAddSkillModal = false">
      <template #header>Add New Skill</template>
      <div class="modal-body">
        <div class="form-group">
          <label>Skill Name</label>
          <input v-model="newSkill.name" type="text" class="form-control" />
        </div>
        <div class="form-group">
          <label>Skill Level</label>
          <select v-model="newSkill.level" class="form-control">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="expert">Expert</option>
          </select>
        </div>
      </div>
      <template #footer>
        <button class="btn btn-secondary" @click="showAddSkillModal = false">
          Cancel
        </button>
        <button class="btn btn-primary" @click="addNewSkill">Add Skill</button>
      </template>
    </modal>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";
import api from "@/services/api.js";
import Multiselect from "vue-multiselect";
import Modal from "@/components/common/Modal.vue";
import FileUpload from "@/components/common/FileUpload.vue";

export default {
  components: { Multiselect, Modal, FileUpload },
  setup() {
    const formData = reactive({
      headline: "",
      experience_years: null,
      linkedin_url: null,
      resume: null,
      resume_path: null,
      user: {
        name: "",
        email: "",
      },
    });

    const rules = {
      user: {
        name: { required },
        email: { required, email },
      },
    };

    const v$ = useVuelidate(rules, formData);

    const skillOptions = ref([]);
    const candidateSkills = ref([]);
    const selectedSkills = ref([]);
    const showAddSkillModal = ref(false);
    const newSkill = reactive({ name: "", level: "intermediate" });
    const loading = ref(false);

    // Get user ID from localStorage
    const userId = JSON.parse(localStorage.getItem("user")).id;

    // Fetch initial data
    const fetchData = async () => {
      try {
        const [profileRes, skillsRes, candidateSkillsRes] = await Promise.all([
          api.candidate.getProfile(),
          api.candidate.getSkills(),
          api.candidate.getCandidateSkills(),
        ]);

        // Populate form data
        Object.assign(formData, profileRes.data);
        skillOptions.value = skillsRes.data;
        candidateSkills.value = candidateSkillsRes.data;
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    // Get skill name by ID
    const getSkillName = (skillId) => {
      const skill = skillOptions.value.find((s) => s.id === skillId);
      return skill ? skill.name : "Unknown Skill";
    };

    // Add new skill
    const addNewSkill = async () => {
      try {
        // First add the skill
        const skillRes = await api.candidate.addSkill(newSkill.name);

        // Then add to candidate skills
        await api.candidate.addCandidateSkill(skillRes.data.id, newSkill.level);

        // Update local state
        skillOptions.value.push(skillRes.data);
        candidateSkills.value.push({
          skill_id: skillRes.data.id,
          level: newSkill.level,
        });

        showAddSkillModal.value = false;
        newSkill.name = "";
        newSkill.level = "intermediate";
      } catch (error) {
        console.error("Error adding skill:", error);
      }
    };

    // Remove skill
    const removeSkill = async (candidateSkillId) => {
      try {
        // await api.delete(`/candidate-skills/${candidateSkillId}`);
        candidateSkills.value = candidateSkills.value.filter(
          (s) => s.id !== candidateSkillId
        );
      } catch (error) {
        console.error("Error removing skill:", error);
      }
    };

    // Handle resume upload
    const handleResumeUpload = (file) => {
      formData.resume = file;
    };
      // Upload resume
    
    // Save profile
    const saveProfile = async () => {
      const isValid = await v$.value.$validate();
      if (!isValid) return;

      loading.value = true;
      try {
        const data = {
          headline: formData.headline,
          experience_years: formData.experience_years,
          linkedin_url: formData.linkedin_url,
          user: {
            name: formData.user.name,
            email: formData.user.email,
          },
        };

        // If new resume uploaded
          if ( formData.resume ) {
            // the file will be added to the formdata
        }

        await api.candidate.updateProfile(data, userId);
        // Show success message
      } catch (error) {
        console.error("Error updating profile:", error);
      } finally {
        loading.value = false;
      }
    };

    onMounted(fetchData);

    return {
      formData,
      v$,
      skillOptions,
      candidateSkills,
      selectedSkills,
      showAddSkillModal,
      newSkill,
      loading,
      getSkillName,
      addNewSkill,
      removeSkill,
      handleResumeUpload,
      saveProfile,
    };
  },
};
</script>

<style scoped>
/* Add your styles matching the Figma design here */
.settings-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.settings-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  border: 1px solid #eee;
  border-radius: 8px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.skill-badge {
  background: #f0f2f5;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.remove-skill {
  border: none;
  background: none;
  color: #666;
  cursor: pointer;
  padding: 0 0.25rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.form-actions {
  margin-top: 2rem;
  text-align: right;
}
</style>
